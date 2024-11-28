<?php
include 'db.php'; // Verbindung zur Datenbank herstellen

if (isset($_GET['location_ID']) && isset($_GET['shop_type_name'])) {
    $location_ID = intval($_GET['location_ID']);
    $shop_type_name = $conn->real_escape_string($_GET['shop_type_name']); // Schutz vor SQL-Injection

    // SQL-Abfrage mit Filter für location_ID und shop_type_name
    $sql = "
        SELECT shop.name AS shop_name, shop_type.name AS shop_type_name
        FROM shop
        JOIN shop_type ON shop.shop_type_ID = shop_type.shop_type_ID
        WHERE shop.location_ID = $location_ID
        AND shop_type.name = '$shop_type_name';
    ";

    // Debugging der SQL-Abfrage
    error_log("SQL Query: " . $sql);

    $result = $conn->query($sql);

    if (!$result) {
        die(json_encode(["error" => "SQL Error: " . $conn->error]));
    }

    $shops = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $shops[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($shops);
} else {
    // Fehler, wenn location_ID oder shop_type_name fehlt
    header('Content-Type: application/json');
    echo json_encode(["error" => "location_ID and shop_type_name are required"]);
}
?>
