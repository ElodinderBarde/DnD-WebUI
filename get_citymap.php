<?php
include 'db.php';

// Debug: Überprüfen, ob die GET-Variable existiert
if (!isset($_GET['city_ID'])) {
    echo json_encode(["error" => "city_ID not provided"]);
    exit;
}

$cityID = intval($_GET['city_ID']); // Sicherstellen, dass city_ID ein Integer ist

// Debug: Stadt-ID anzeigen
error_log("city_ID: $cityID");

$stmt = $conn->prepare("SELECT citymap FROM city WHERE city_ID = ?");
if (!$stmt) {
    echo json_encode(["error" => "Statement preparation failed"]);
    error_log("MySQL Error: " . $conn->error);
    exit;
}

$stmt->bind_param("i", $cityID);

// Debug: Statement ausführen
if (!$stmt->execute()) {
    echo json_encode(["error" => "Statement execution failed"]);
    error_log("Execution Error: " . $stmt->error);
    exit;
}

$stmt->bind_result($cityMap);

if ($stmt->fetch()) {
    // Debug: Ergebnis ausgeben
    echo json_encode(["citymap" => $cityMap]);
} else {
    // Debug: Kein Ergebnis gefunden
    echo json_encode(["error" => "No result found for city_ID"]);
    error_log("No result found for city_ID: $cityID");
}

$stmt->close();
?>
