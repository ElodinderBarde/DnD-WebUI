<?php
include '../db.php'; // Verbindung zur Datenbank herstellen
$input = json_decode(file_get_contents("php://input"), true) ?? [];
if (!is_array($input)) {
    $input = [];
}


error_log("Eingehende Daten: " . print_r($input, true)); // Log-Eingabe

$data = []; // Ergebnisarray für den NPC

// Rasse manuell oder zufällig bestimmen
if (isset($input['race']) && !empty($input['race'])) {
    
    // Manuell ausgewählte Rasse abrufen
    $raceID = intval($input['race']);
    $data['race'] = getRowByID('race', 'race_ID', $raceID, 'racename');
} else {
    // Gewichtete Zufällige Rasse auswählen
    $data['race'] = getRandomRace();
}

// Klassen-Logik (manuelle Auswahl oder zufällige Generierung)
if (isset($input['allow_classes']) && $input['allow_classes'] === true) {
    if (isset($input['manual_class']) && !empty($input['manual_class'])) {
        // Manuelle Klasse und Subklasse
        $classID = intval($input['manual_class']);
        $data['npc_class'] = getRowByID('npc_class', 'npc_class_ID', $classID, 'name');

        if (isset($input['manual_subclass']) && !empty($input['manual_subclass'])) {
            $subclassID = intval($input['manual_subclass']);
            $data['npc_subclass'] = getRowByID('npc_subclass', 'npc_subclass_ID', $subclassID, 'name');
        } else {
            // Zufällige Subklasse basierend auf der Klasse
            $subclasses = getSubclassesByClass($classID);
            if (!empty($subclasses)) {
                $randomIndex = array_rand($subclasses);
                $data['npc_subclass'] = $subclasses[$randomIndex];
            } else {
                $data['npc_subclass'] = ["id" => null, "value" => "Keine Subklassen verfügbar"];
            }
        }
    } else {
        $data['npc_class'] = getRandomRow('npc_class', 'npc_class_ID', 'name');
        $classID = $data['npc_class']['id'];
        $subclasses = getSubclassesByClass($classID);

        if (!empty($subclasses)) {
            $randomIndex = array_rand($subclasses);
            $data['npc_subclass'] = $subclasses[$randomIndex];
        } else {
            $data['npc_subclass'] = ["id" => null, "value" => "Keine Subklassen verfügbar"];
        }
    }
} else {
    $data['npc_class'] = ["id" => null, "value" => "Keine Klasse erlaubt"];
    $data['npc_subclass'] = ["id" => null, "value" => "Keine Subklassen verfügbar"];
}

// Weitere Felder für den NPC generieren
$data['npc_fullname'] = generateFullName();
$data['npc_gender'] = getRandomRow('npc_gender', 'npc_gender_ID', 'npc_gender');
$data['npc_age'] = generateAge($data['race']['id'] ?? null);
$data['npc_betonung'] = getRandomRow('npc_betonung', 'npc_betonung_ID', 'betonung');
$data['npc_talkingstyle'] = getRandomRow('npc_talkingstyle', 'npc_talkingstyle_ID', 'description');
$data['npc_background'] = getRandomRow('npc_background', 'npc_background_ID', 'name');
$data['npc_personality'] = getRandomRow('npc_personality', 'npc_personality_ID', 'description');
$data['npc_likes'] = getRandomRow('npc_likes', 'npc_likes_ID', 'description');
$data['npc_dislikes'] = getRandomRow('npc_dislikes', 'npc_dislikes_ID', 'description');
$data['npc_haircolor'] = getRandomRow('npc_haircolor', 'npc_haircolor_ID', 'name');
$data['npc_hairstyle'] = getRandomRow('npc_hairstyle', 'npc_hairstyle_ID', 'name');
$data['npc_beardstyle'] = ($data['npc_gender']['id'] == 1) ? getRandomRow('npc_beardstyle', 'npc_beardstyle_ID', 'name') : null;
$data['npc_jackets'] = getRandomRow('npc_jackets', 'npc_jackets_ID', 'name');
$data['npc_trousers'] = getRandomRow('npc_trousers', 'npc_trousers_ID', 'name');
$data['npc_kleidungsqualität'] = getRandomRow('npc_kleidungsqualität', 'npc_kleidungsqualität_ID', 'description');
$data['npc_flaw'] = getRandomRow('npc_flaw', 'npc_flaw_ID', 'description');
$data['npc_ideals'] = getRandomRow('npc_ideals', 'npc_ideals_ID', 'description');
$data['npc_jewellery'] = getRandomRow('npc_jewellery', 'npc_jewellery_ID', 'name');
$data['npc_other_description'] = getRandomRow('npc_other_description', 'npc_other_description_ID', 'description');

//error_log("\nData NPC: " . print_r($data, true)); // Log-Eingabe


// Rollenzuweisung basierend auf Alter
if ($data['npc_age']['value'] >= 60) {
    $data['role'] = "grandparent";
} elseif ($data['npc_age']['value'] >= 30) {
    $data['role'] = "parent";
} else {
    $data['role'] = "aunt_uncle";
}

// Rückgabe der Daten
header('Content-Type: application/json');
echo json_encode($data);
exit;

// ---------------------- Hilfsfunktionen ----------------------

// Zeile nach ID aus einer Tabelle abrufen
function getRowByID($tableName, $idColumn, $id, $valueColumn) {
    global $conn;
    $query = "SELECT $idColumn, $valueColumn FROM $tableName WHERE $idColumn = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return ["id" => $row[$idColumn], "value" => $row[$valueColumn]];
    }
    return ["id" => null, "value" => "N/A"];
}

// Zufällige Zeile aus einer Tabelle abrufen
function getRandomRow($tableName, $idColumn, $valueColumn) {
    global $conn;
    $query = "SELECT $idColumn, $valueColumn FROM $tableName ORDER BY RAND() LIMIT 1";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return ["id" => $row[$idColumn], "value" => $row[$valueColumn]];
    }
    return ["id" => null, "value" => "N/A"];
}

// Alter basierend auf der Rasse bestimmen
function generateAge($raceID) {
    global $conn;
    if ($raceID) {
        $query = "SELECT adultage, maxage FROM race WHERE race_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $raceID);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return ["id" => null, "value" => rand($row['adultage'], $row['maxage'])];
        }
    }
    return ["id" => null, "value" => rand(18, 100)];
}

// Vollständigen Namen generieren
// Funktion, um den vollen Namen zu generieren
function generateFullName($familyLastname = null) {
    // Vorname aus der Datenbank abrufen
    $firstname = getRandomRow('npc_firstname', 'npc_firstname_ID', 'firstname');

    // Nachnamen entweder generieren oder übernehmen
    if ($familyLastname === null) {
        $lastname = getRandomRow('npc_lastname', 'npc_lastname_ID', 'lastname');
        $familyLastname = $lastname['value']; // Speichere für Familie
    }
    
    // Fullname zusammensetzen
    return [
        "id" => null,
        "value" => $firstname['value'] . ' ' . $familyLastname,
        "firstname" => $firstname['value'],
        "lastname" => $familyLastname
    ];
}




function generateLastName() {
    $result = getRandomRow('npc_lastname', 'npc_lastname_ID', 'lastname');
    return $result['value'] ?? "Unknown";
}





// Familie generieren
if (isset($input['generate_family']) && $input['generate_family'] === true) {
    $response['family'] = callGetFamilies($familyLastname, $raceID, $allowClasses);
}

header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT); // Optional: JSON_PRETTY_PRINT für lesbare Ausgabe
exit;

// Funktion für Familie
function callGetFamilies($familyLastname, $raceID, $allowClasses) {
    $input = [
        "generate_family" => true,
        "family_lastname" => $familyLastname,
        "race" => $raceID,
        "allow_classes" => $allowClasses,
    ];

    error_log("Massives Input Variabe: " . print_r($input, true)); // Log-Eingabe

    $url = 'get_families.php';
    $options = [
        'http' => [
            'header' => "Content-type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($input),
        ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true)['family'] ?? [];
}



// Subklassen filtern basierend auf der Klasse
function getSubclassesByClass($classID) {
    global $conn;

    $query = "SELECT npc_subclass_ID AS id, name AS value FROM npc_subclass WHERE npc_class_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $classID);
    $stmt->execute();
    $result = $stmt->get_result();

    $subclasses = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $subclasses[] = $row;
        }
    }
    return $subclasses;
}

// Verarbeitung für Dropdown-Subklassen
if (isset($input['action']) && $input['action'] === 'get_subclasses') {
    if (isset($input['manual_class']) && !empty($input['manual_class'])) {
        $classID = intval($input['manual_class']);
        $subclasses = getSubclassesByClass($classID);

        // Rückgabe der Subklassen für das Dropdown-Menü
        header('Content-Type: application/json');
        echo json_encode(["subclasses" => $subclasses]);
        exit;
    } else {
        // Keine Klasse ausgewählt
        header('Content-Type: application/json');
        echo json_encode(["subclasses" => []]);
        exit;
    }
}
// Generiere Familie, falls Checkbox aktiviert
if (isset($input['generate_family']) && $input['generate_family'] === true) {
    // Familiennachname für alle Mitglieder verwenden
    //error_log("Blubber");
    //error_log("Nachname: " . print_r($data['npc_fullname']['lastname'], true)); // Log-Eingabe

    $familyLastname = $data['npc_fullname']['lastname'];

    
    // Generiere die Familie basierend auf der Rasse und füge sie hinzu
    $familyData = generateFamily($data['race']['id'], $familyLastname);
    $data['family'] = integrateNPCIntoFamily($data, $familyData);
}


header('Content-Type: application/json');
echo json_encode($data, JSON_PRETTY_PRINT); // Optional: JSON_PRETTY_PRINT für lesbare Ausgabe
exit;




// Manuelle Klasse auswählen und zugehörige Subklassen abrufen
if (isset($input['manual_class']) && !empty($input['manual_class'])) {
    $classID = intval($input['manual_class']);
    $data['npc_class'] = getRowByID('npc_class', 'npc_class_ID', $classID, 'name');
    $data['npc_subclasses'] = getSubclassesByClass($classID); // Nur Subklassen dieser Klasse
}
function getRandomRace() {
    global $conn;

    // Alle Rassen und ihre Verbreitungswerte abrufen
    $query = "SELECT race_ID, racename, verbreitung_in_promille FROM race";
    $result = $conn->query($query);

    if (!$result) {
        error_log("Fehler bei der Abfrage der Rassen: " . $conn->error);
        return ["id" => null, "value" => "Keine Rasse verfügbar"];
    }

    $weightedPool = [];
    while ($row = $result->fetch_assoc()) {
        for ($i = 0; $i < $row['verbreitung_in_promille']; $i++) {
            $weightedPool[] = $row;
        }
    }

    if (empty($weightedPool)) {
        error_log("Gewichteter Pool ist leer. Überprüfen Sie die Verbreitungswerte.");
        return ["id" => null, "value" => "Keine Rasse verfügbar"];
    }

    $randomIndex = array_rand($weightedPool);
    return ["id" => $weightedPool[$randomIndex]['race_ID'], "value" => $weightedPool[$randomIndex]['racename']];
}
function generateFamily($raceID, $familyLastname) {
    $family = [
        "parents" => [],
        "children" => [],
        "grandparents" => [],
        "aunts_uncles" => [],
    ];

    // Eltern generieren
    $family["parents"][] = generateRandomNPC($raceID, "parent", $familyLastname);
    $family["parents"][] = generateRandomNPC($raceID, "parent", $familyLastname);

    // Kinder generieren
    for ($i = 0; $i < rand(1, 4); $i++) {
        $family["children"][] = generateRandomNPC($raceID, "child", $familyLastname);
    }

    // Großeltern generieren
    for ($i = 0; $i < rand(0, 2); $i++) {
        $family["grandparents"][] = generateRandomNPC($raceID, "grandparent", $familyLastname);
    }

    // Onkel/Tanten generieren
    for ($i = 0; $i < rand(0, 3); $i++) {
        $family["aunts_uncles"][] = generateRandomNPC($raceID, "aunt_uncle", $familyLastname);
    }

    return $family;
}

function integrateNPCIntoFamily($npc, $family) {
    // Alter des NPC prüfen und Rolle zuweisen
    if ($npc['npc_age']['value'] >= 60) {
        $npc['role'] = "grandparent"; // Über 60 Jahre: Großeltern
        $family['grandparents'][] = $npc;
    } elseif ($npc['npc_age']['value'] >= 20) {
        $npc['role'] = "parent"; // 20 bis 59 Jahre: Elternteil
        $family['parents'][] = $npc;
    } else {
        $npc['role'] = "aunt_uncle"; // Unter 20 Jahre: Onkel/Tante
        $family['aunts_uncles'][] = $npc;
    }
    return $family;
}
// Familiennachname für alle Mitglieder übernehmen
function generateRandomNPC($raceID, $role, $familyLastname) {
    return [
        "firstname" => getRandomRow('npc_firstname', 'npc_firstname_ID', 'firstname')["value"],
        "lastname" => $familyLastname,
        "npc_age" => ["value" => rand(20, 100)], // Alter zufällig generieren
        "role" => $role,
        "race" => getRowByID('race', 'race_ID', $raceID, 'racename'),
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
exit;