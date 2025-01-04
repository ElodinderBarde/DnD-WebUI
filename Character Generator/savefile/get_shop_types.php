<?php
include 'db.php'; // Verbindung zur Datenbank herstellen

// Überprüfung, ob location_ID übergeben wurde
if (isset($_GET['location_ID'])) {
    $location_ID = intval($_GET['location_ID']); // Stelle sicher, dass es eine Zahl ist

    // SQL-Abfrage, um einzigartige Shop-Typen zu laden
    $sql = "
    SELECT DISTINCT shop_type.name AS shop_type_name
    FROM shop
    JOIN shop_type ON shop.shop_type_ID = shop_type.shop_type_ID
    WHERE shop.location_ID = $location_ID
    ";

    // Debug: SQL anzeigen
    error_log("SQL Query: " . $sql);

    $result = $conn->query($sql);

    if (!$result) {
        die("SQL Error: " . $conn->error); // Gibt den genauen SQL-Fehler aus
    }

    $shopTypes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $shopTypes[] = $row;
        }
    }

    // JSON-Ausgabe
    header('Content-Type: application/json');
    echo json_encode($shopTypes);
} else {
    // Fehlerausgabe, wenn location_ID fehlt
    header('Content-Type: application/json');
    echo json_encode(['error' => 'location_ID is required']);
}



?>