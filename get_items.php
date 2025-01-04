<?php
include 'db.php'; // Verbindung zur Datenbank herstellen

if (isset($_GET['shop_ID'])) {
    $shop_ID = intval($_GET['shop_ID']); // Sicherstellen, dass shop_ID eine Zahl ist

    // SQL-Abfrage, um Items für einen Shop zu laden
    $sql = "

 SELECT 
 si.shop_item_id,
 si.shop_ID,
 si.itemID,
 si.quantity,
 si.special_price,
 si.discount,
 i.itemName,
 i.`price ( gold)` AS base_price, -- Alias für base_price
 i.Typ AS type                    -- Alias für type
FROM shop_items si
JOIN items i ON si.itemID = i.itemID
WHERE si.shop_ID = ?;
";



    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(["error" => "SQL-Fehler: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $shop_ID);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'itemID' => $row['itemID'],
            'itemName' => $row['itemName'],
            'base_price' => $row['base_price'],
            'type' => $row['type'],
            'quantity' => $row['quantity'],
            'special_price' => $row['special_price'],
            'discount' => $row['discount']
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($items);
} else {
    header('Content-Type: application/json');
    echo json_encode(["error" => "shop_ID is required"]);
}
?>
