<?php
include '../db.php'; // Verbindung zur Datenbank herstellen
function getRandomRow($tableName, $idColumn, $valueColumn, $excludeOther = false) {
    global $conn;
    $query = "SELECT $idColumn, $valueColumn FROM $tableName";
    if ($excludeOther && $tableName === 'npc_gender') {
        $query .= " WHERE $valueColumn != 'Other'";
    }
    $query .= " ORDER BY RAND() LIMIT 1";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return ["$idColumn" => null, "$valueColumn" => "N/A"];
}



$input = json_decode(file_get_contents("php://input"), true);
error_log("Eingehende Daten (Familie1): " . print_r($input, true));

$response = ["family" => []];

if (isset($input['generate_family']) && $input['generate_family'] === true) {
    $raceID = isset($input['race']) ? intval($input['race']) : null;
    $lastname = isset($input['lastname']) ? $input['lastname'] : generateLastName(); // Nachname übernehmen
    $allowClasses = isset($input['allow_classes']) && $input['allow_classes'];
    $manualClass = isset($input['manual_class']) ? intval($input['manual_class']) : null;
    

    // Wenn keine Race ID vorgegeben ist, generiere eine zufällige Race ID
    if (!$raceID) {
        $race = getRandomRow('race', 'race_ID', 'racename');
        $raceID = $race['race_ID'];
    }

    // Familie initialisieren
    $family = [
        "parents" => [],
        "children" => [],
        "grandparents" => [],
        "aunts_uncles" => [],
    ];

    // Eltern generieren
    for ($i = 0; $i < 2; $i++) {
        $parentAge = rand(25, 40); // Eltern sind zwischen 25 und 40 Jahre alt
        $family["parents"][] = generateNPC($raceID, $lastname, $parentAge, "parent", $allowClasses, $manualClass);
    }

    // Kinder generieren
    $numChildren = rand(1, 4);
    foreach (range(1, $numChildren) as $index) {
        $parentAge = $family["parents"][0]["age"];
        $childAge = rand(1, max($parentAge - 18, 1)); // Kinder sind jünger als Eltern
        $family["children"][] = generateNPC($raceID, $lastname, $childAge, "child", false); // Keine Klassen für Kinder
    }

    // Großeltern generieren
    if (rand(0, 1)) {
        foreach (range(1, rand(1, 2)) as $index) {
            $grandparentAge = rand(50, 80);
            $family["grandparents"][] = generateNPC($raceID, $lastname, $grandparentAge, "grandparent", $allowClasses, $manualClass);
        }
    }

    // Onkel/Tanten generieren
    if (rand(0, 1)) {
        foreach (range(1, rand(1, 2)) as $index) {
            $auntUncleAge = rand(20, 40);
            $family["aunts_uncles"][] = generateNPC($raceID, $lastname, $auntUncleAge, "aunt_uncle", $allowClasses, $manualClass);
        }
    }

    $response["family"] = $family;
    //error_log("Familien-Daten vor der Rückgabe: " . print_r($response, true));
}



header('Content-Type: application/json');
echo json_encode($response);
exit;

// ---------------------- Hilfsfunktionen ----------------------
function generateNPC($raceID, $lastname, $age, $role, $allowClasses = false, $manualClass = null) {
    global $conn;
    $firstname = generateFirstName();
    $gender = getRandomRow('npc_gender', 'npc_gender_ID', 'npc_gender', true); // Exclude "Other"

    // Klasse zufällig oder manuell zuweisen, wenn nicht Kind
    $npcClass = null;
    if ($allowClasses && $role !== "child") {
        if ($manualClass) {
            // Manuell ausgewählte Klasse zuweisen
            $npcClass = getRowByID('npc_class', 'npc_class_ID', $manualClass, 'name');
        } elseif (rand(1, 100) <= 5) {
            // Mit 5% Wahrscheinlichkeit zufällige Klasse zuweisen
            $npcClass = getRandomRow('npc_class', 'npc_class_ID', 'name');
        }
    }

    return [
        "firstname" => $firstname,
        "lastname" => $lastname,
        "age" => $age,
        "gender" => $gender,
        "race" => getRowByID('race', 'race_ID', $raceID, 'racename'),
        "role" => $role,
        "class" => $npcClass, // Klasse hinzufügen
        "flaw" => getRandomRow('npc_flaw', 'npc_flaw_ID', 'description'), // Makel hinzufügen
        "ideal" => getRandomRow('npc_ideals', 'npc_ideals_ID', 'description'), // Ideale hinzufügen
        "personality" => getRandomRow('npc_personality', 'npc_personality_ID', 'description'), // Persönlichkeit
        "background" => getRandomRow('npc_background', 'npc_background_ID', 'name'), // Hintergrund
        "likes" => getRandomRow('npc_likes', 'npc_likes_ID', 'description'), // Vorlieben
        "dislikes" => getRandomRow('npc_dislikes', 'npc_dislikes_ID', 'description'), // Abneigungen
        "hairstyle" => getRandomRow('npc_hairstyle', 'npc_hairstyle_ID', 'name'), // Frisur
        "haircolor" => getRandomRow('npc_haircolor', 'npc_haircolor_ID', 'name'), // Haarfarbe
        "beardstyle" => ($gender['npc_gender'] === 'Male') ? getRandomRow('npc_beardstyle', 'npc_beardstyle_ID', 'name') : null, // Bartstil nur für Männer
        "clothing_jacket" => getRandomRow('npc_jackets', 'npc_jackets_ID', 'name'), // Kleidung: Jacke
        "clothing_trousers" => getRandomRow('npc_trousers', 'npc_trousers_ID', 'name'), // Kleidung: Hose
        "clothing_quality" => getRandomRow('npc_kleidungsqualität', 'npc_kleidungsqualität_ID', 'description'), // Kleidungsqualität
    ];
    
}




function generateFirstName() {
    $result = getRandomRow('npc_firstname', 'npc_firstname_ID', 'firstname');
    return $result['firstname'] ?? "Unknown";
}

function generateLastName() {
    $result = getRandomRow('npc_lastname', 'npc_lastname_ID', 'lastname');
    return $result['lastname'] ?? "Unknown";
}



function getRowByID($tableName, $idColumn, $id, $valueColumn) {
    global $conn;
    $query = "SELECT $idColumn, $valueColumn FROM $tableName WHERE $idColumn = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return ["$idColumn" => null, "$valueColumn" => "N/A"];
}
