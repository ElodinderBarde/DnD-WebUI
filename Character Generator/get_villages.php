<?php
include 'db.php'; // Verbindung zur Datenbank herstellen

// SQL-Abfrage für Villages
$sql = "
    SELECT location.location_ID, location.village_name
    FROM location
    WHERE location.village_name IS NOT NULL
";

// Debug: SQL anzeigen
error_log("SQL Query: " . $sql);

$result = $conn->query($sql);

if (!$result) {
    die("SQL Error: " . $conn->error); // SQL-Fehler ausgeben
}

$villages = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $villages[] = $row;
    }
}

// Debug: Ergebnis anzeigen
error_log("Villages gefunden: " . json_encode($villages));

// JSON-Ausgabe
header('Content-Type: application/json');
echo json_encode($villages);
?>
