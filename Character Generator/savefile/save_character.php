<?php
header('Content-Type: application/json');

include '../db.php'; // Verbindung zur Datenbank
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

try {
    // Rohdaten aus php://input lesen
    $rawInput = file_get_contents('php://input');
    error_log("Rohdaten von php://input: " . $rawInput);

    // Überprüfen, ob die Eingabe leer ist
    if (empty(trim($rawInput))) {
        throw new Exception("Keine JSON-Daten empfangen.");
    }

    // JSON-Daten dekodieren
    $inputData = json_decode($rawInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Ungültige JSON-Daten: " . json_last_error_msg());
    }

    // Debugging: Zeige die dekodierten JSON-Daten an
    error_log("Dekodierte JSON-Daten: " . print_r($inputData, true));

    // Validierung von npc_fullname
    if (!isset($inputData['npc_fullname']) || !is_array($inputData['npc_fullname'])) {
        throw new Exception("npc_fullname fehlt oder ist kein gültiges Array.");
    }

    // Vor- und Nachnamen extrahieren
    $firstname = $inputData['npc_fullname']['firstname'] ?? null;
    $lastname = $inputData['npc_fullname']['lastname'] ?? null;

    if (!$firstname) {
        throw new Exception("Vorname fehlt in npc_fullname.");
    }

    // Debugging: Zeige Vor- und Nachnamen an
    error_log("Vorname: $firstname, Nachname: $lastname");

    // Nur POST-Anfragen erlauben
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['message' => 'Nur POST-Anfragen erlaubt.']);
        exit;
    }

    // Vorname und Nachname abrufen oder erstellen
    $fullnameID = getOrInsertFullname($conn, $firstname, $lastname);

    // NPC-Daten sammeln
    $npcData = [
        'npc_fullname_ID' => $fullnameID,
        'gender_ID' => $inputData['npc_gender']['id'] ?? null,
        'age_ID' => $inputData['npc_age']['id'] ?? null,
        'race_ID' => $inputData['race']['id'] ?? null,
        'class_ID' => $inputData['npc_class']['id'] ?? null,
        'background_ID' => $inputData['npc_background']['id'] ?? null,
        'beardstyle_ID' => $inputData['npc_beardstyle']['id'] ?? null,
        'betonung_ID' => $inputData['npc_betonung']['id'] ?? null,
        'dislikes_ID' => $inputData['npc_dislikes']['id'] ?? null,
        'haircolor_ID' => $inputData['npc_haircolor']['id'] ?? null,
        'hairstyle_ID' => $inputData['npc_hairstyle']['id'] ?? null,
        'jackets_ID' => $inputData['npc_jackets']['id'] ?? null,
        'kleidungsqualität_ID' => $inputData['npc_kleidungsqualität']['id'] ?? null,
        'likes_ID' => $inputData['npc_likes']['id'] ?? null,
        'personality_ID' => $inputData['npc_personality']['id'] ?? null,
        'talkingstyle_ID' => $inputData['npc_talkingstyle']['id'] ?? null,
        'trousers_ID' => $inputData['npc_trousers']['id'] ?? null
    ];

    // Debugging: Zeige die gesammelten NPC-Daten
    error_log("NPC-Daten vor dem Speichern: " . print_r($npcData, true));

    // NPC speichern
    saveNpc($conn, $npcData);

    // Erfolgsnachricht zurückgeben
    echo json_encode(['message' => 'NPC erfolgreich gespeichert!']);

} catch (Exception $e) {
    http_response_code(400); // Bad Request
    error_log("Fehler: " . $e->getMessage());
    echo json_encode(['message' => 'Fehler: ' . $e->getMessage()]);
    exit;
}

// Funktion, um Vor- und Nachnamen zu speichern oder abzurufen
function getOrInsertFullname($conn, $firstname, $lastname)
{
    $stmt = $conn->prepare("
        SELECT npc_fullname_ID 
        FROM npc_fullname 
        WHERE npc_firstname_ID = (SELECT npc_firstname_ID FROM npc_firstname WHERE firstname = ?) 
        AND npc_lastname_ID <=> (SELECT npc_lastname_ID FROM npc_lastname WHERE lastname = ?)
    ");
    $stmt->bind_param("ss", $firstname, $lastname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['npc_fullname_ID'];
    }

    // Falls nicht vorhanden, Vorname und Nachname einfügen
    $firstnameID = getOrInsertFirstname($conn, $firstname);
    $lastnameID = $lastname ? getOrInsertLastname($conn, $lastname) : null;

    $stmt = $conn->prepare("INSERT INTO npc_fullname (npc_firstname_ID, npc_lastname_ID) VALUES (?, ?)");
    $stmt->bind_param("ii", $firstnameID, $lastnameID);
    if (!$stmt->execute()) {
        throw new Exception("Fehler beim Einfügen des vollständigen Namens: " . $stmt->error);
    }
    return $stmt->insert_id;
}

// Funktion, um NPC-Daten zu speichern
function saveNpc($conn, $npcData)
{
    $stmt = $conn->prepare("
        INSERT INTO npc (
            npc_fullname_ID, npc_gender_ID, npc_age_ID, race_ID, npc_class_ID, npc_background_ID,
            npc_beardstyle_ID, npc_betonung_ID, npc_dislikes_ID, npc_haircolor_ID, npc_hairstyle_ID,
            npc_jackets_ID, npc_kleidungsqualität_ID, npc_likes_ID, npc_personality_ID, npc_talkingstyle_ID,
            npc_trousers_ID
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "iiiiiiiiiiiiiiiii",
        $npcData['npc_fullname_ID'],
        $npcData['gender_ID'],
        $npcData['age_ID'],
        $npcData['race_ID'],
        $npcData['class_ID'],
        $npcData['background_ID'],
        $npcData['beardstyle_ID'],
        $npcData['betonung_ID'],
        $npcData['dislikes_ID'],
        $npcData['haircolor_ID'],
        $npcData['hairstyle_ID'],
        $npcData['jackets_ID'],
        $npcData['kleidungsqualität_ID'],
        $npcData['likes_ID'],
        $npcData['personality_ID'],
        $npcData['talkingstyle_ID'],
        $npcData['trousers_ID']
    );
    if (!$stmt->execute()) {
        throw new Exception("Fehler beim Speichern des NPC: " . $stmt->error);
    }
}

// Funktion, um Vorname zu speichern oder ID zu holen
function getOrInsertFirstname($conn, $firstname)
{
    $stmt = $conn->prepare("SELECT npc_firstname_ID FROM npc_firstname WHERE firstname = ?");
    $stmt->bind_param("s", $firstname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['npc_firstname_ID'];
    }

    $stmt = $conn->prepare("INSERT INTO npc_firstname (firstname) VALUES (?)");
    $stmt->bind_param("s", $firstname);
    if (!$stmt->execute()) {
        throw new Exception("Fehler beim Einfügen des Vornamens.");
    }
    return $stmt->insert_id;
}

// Funktion, um Nachname zu speichern oder ID zu holen
function getOrInsertLastname($conn, $lastname)
{
    $stmt = $conn->prepare("SELECT npc_lastname_ID FROM npc_lastname WHERE lastname = ?");
    $stmt->bind_param("s", $lastname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['npc_lastname_ID'];
    }

    $stmt = $conn->prepare("INSERT INTO npc_lastname (lastname) VALUES (?)");
    $stmt->bind_param("s", $lastname);
    if (!$stmt->execute()) {
        throw new Exception("Fehler beim Einfügen des Nachnamens.");
    }
    return $stmt->insert_id;
}
