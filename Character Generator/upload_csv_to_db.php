<?php
header('Content-Type: application/json');
include '../db.php'; // Verbindung zur Datenbank

try {
    $filename = 'cache/npc_data.csv';
    if (!file_exists($filename) || filesize($filename) === 0) {
        throw new Exception("Die CSV-Datei ist leer oder existiert nicht.");
    }

    $file = fopen($filename, 'r');
    $header = fgetcsv($file); // Erste Zeile als Header lesen

    $transactionStarted = false; // Variable, um den Transaktionsstatus zu verfolgen

    $conn->begin_transaction();
    $transactionStarted = true;

    while (($data = fgetcsv($file)) !== false) {
        // CSV-Zeile mit den Datenbanken-Spalten zuordnen
        $stmt = $conn->prepare("
            INSERT INTO npc (
                npc_fullname_ID, gender_ID, age_ID, race_ID, class_ID, background_ID,
                beardstyle_ID, betonung_ID, dislikes_ID, haircolor_ID, hairstyle_ID,
                jackets_ID, kleidungsqualität_ID, likes_ID, personality_ID, talkingstyle_ID,
                trousers_ID
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iiiiiiiiiiiiiiiii",
            $data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6],
            $data[7], $data[8], $data[9], $data[10], $data[11], $data[12], $data[13],
            $data[14], $data[15], $data[16]
        );
        if (!$stmt->execute()) {
            throw new Exception("Fehler beim Einfügen in die Datenbank: " . $stmt->error);
        }
    }
    $conn->commit();
    fclose($file);

    // Leere die CSV-Datei nach erfolgreicher Speicherung
    clearCSV($filename);

    echo json_encode(['message' => 'Alle NPCs erfolgreich in die Datenbank übertragen.']);
} catch (Exception $e) {
    if ($transactionStarted) {
        $conn->rollback();
    }
    http_response_code(500);
    echo json_encode(['message' => 'Fehler: ' . $e->getMessage()]);
    exit;
}

function clearCSV($filename = 'cache/npc_data.csv') {
    $file = fopen($filename, 'w');
    fclose($file);
}
