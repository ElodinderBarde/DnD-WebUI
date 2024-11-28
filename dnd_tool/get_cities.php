<?php
include 'db.php'; // Verbindung zur Datenbank herstellen

$sql = "
    SELECT city.city_ID, city.city_name, location.location_ID
    FROM location
    JOIN city ON location.city_name = city.city_name
";

$result = $conn->query($sql);

$cities = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cities[] = $row; // city_name, city_ID und location_ID werden abgerufen
    }
}

// JSON-Ausgabe
header('Content-Type: application/json');
echo json_encode($cities);
?>
