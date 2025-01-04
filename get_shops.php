<?php
include 'db.php'; // Datenbankverbindung

if (isset($_GET['location_ID']) && isset($_GET['shop_type_name'])) {
    $location_ID = intval($_GET['location_ID']);
    $shop_type_name = $_GET['shop_type_name'];

    $sql = "
        SELECT shop.shop_ID, shop.name AS shop_name, shop_type.name AS shop_type_name
        FROM shop
        JOIN shop_type ON shop.shop_type_ID = shop_type.shop_type_ID
        WHERE shop.location_ID = ? AND shop_type.name = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $location_ID, $shop_type_name);
    $stmt->execute();
    $result = $stmt->get_result();

    $response = [];
    while ($row = $result->fetch_assoc()) {
        $response[] = [
            'shop_ID' => $row['shop_ID'],
            'shop_name' => $row['shop_name'],
            'shop_type_name' => $row['shop_type_name']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    echo json_encode(["error" => "location_ID and shop_type_name are required"]);
}
?>
