<?php
include 'db.php'; // Verbindung zur Datenbank herstellen

if (isset($_GET['city_ID'])) {
    $city_ID = intval($_GET['city_ID']); // Sicherstellen, dass es eine Zahl ist

    // SQL-Abfrage, um location_ID zu finden
    $sql = "SELECT location_ID FROM city WHERE city_ID = $city_ID";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $location = $result->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode($location);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'location_ID not found for city_ID']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'city_ID is required']);
}
?>
