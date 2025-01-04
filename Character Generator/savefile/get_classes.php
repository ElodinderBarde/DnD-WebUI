<?php
include '../db.php'; // Verbindung zur Datenbank herstellen

// JSON-Eingabedaten auslesen
$input = json_decode(file_get_contents("php://input"), true);
error_log("Eingehende Daten (get_classes.php): " . print_r($input, true)); // Debugging

$response = [];

// Aktion prüfen
if (isset($input['action'])) {
    // Subklassen basierend auf Klasse abrufen
    if ($input['action'] === 'get_subclasses') {
        error_log("Aktion: get_subclasses"); // Debugging-Ausgabe

        if (isset($input['class_id']) && !empty($input['class_id'])) {
            $classID = intval($input['class_id']);
            error_log("Empfangene class_id: " . $classID); // Debugging-Ausgabe

            $subclasses = getSubclassesByClass($classID);

            header('Content-Type: application/json');
            echo json_encode(["subclasses" => $subclasses]);
            exit;
        } else {
            error_log("Keine gültige class_id empfangen."); // Debugging-Ausgabe

            header('Content-Type: application/json');
            echo json_encode(["subclasses" => []]);
            exit;
        }
    }

    // Klassen abrufen
    if ($input['action'] === 'get_classes') {
        error_log("Aktion: get_classes"); // Debugging-Ausgabe

        $classes = getAllClasses();
        $response['classes'] = $classes;

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Standard-Antwort, wenn keine gültige Aktion übergeben wurde
header('Content-Type: application/json');
echo json_encode(["error" => "Ungültige Aktion"]);
exit;

// ---------------------- Hilfsfunktionen ----------------------

/**
 * Alle Klassen abrufen
 */
function getAllClasses() {
    global $conn;
    $query = "SELECT npc_class_ID AS id, name AS value FROM npc_class";
    $result = $conn->query($query);

    $classes = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $classes[] = $row;
        }
    }
    return $classes;
}

/**
 * Subklassen basierend auf der Klasse abrufen
 */
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
