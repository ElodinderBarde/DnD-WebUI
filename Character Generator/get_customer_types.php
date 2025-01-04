<?php
include 'db.php'; // Verbindung zur Datenbank herstellen

header('Content-Type: application/json');

$sql = "
    SELECT DISTINCT
        shop_customer.Position AS position
    FROM shop_customer
    WHERE shop_customer.Position IS NOT NULL;
";

$result = $conn->query($sql);
if ($result) {
    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = [
            'position' => $row['position']
        ];
    }
    echo json_encode($employees);
} else {
    echo json_encode(["error" => "Datenbankfehler: " . $conn->error]);
}
?>
