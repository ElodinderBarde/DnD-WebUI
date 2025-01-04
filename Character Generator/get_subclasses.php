<?php
include '../db.php'; // Verbindung zur Datenbank herstellen

// JSON-Eingabedaten auslesen
$input = json_decode(file_get_contents("php://input"), true);
error_log("Eingehende Daten (get_subclasses.php): " . print_r($input, true)); // Debugging

if (isset($input['class_id']) && !empty($input['class_id'])) {
    $classID = intval($input['class_id']);
    error_log("Empfangene class_id: " . $classID); // Debugging

    // Subklassen basierend auf class_id abrufen
    $query = "SELECT npc_subclass_ID, name FROM npc_subclass WHERE npc_class_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $classID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    error_log("Abfrage ausgeführt: " . $query . " mit classID=" . $classID);
    
    $subclasses = [];
    while ($row = $result->fetch_assoc()) {
        error_log("Gefundene Subklasse: " . json_encode($row));
        $subclasses[] = [
            "id" => $row["npc_subclass_ID"],
            "value" => $row["name"]
        ];
    }
    header('Content-Type: application/json');
    echo json_encode(["subclasses" => $subclasses]);
    exit;
} else {
    error_log("Keine gültige class_id empfangen."); // Debugging
    header('Content-Type: application/json');
    echo json_encode(["subclasses" => []]);
    exit;
}
?>
