<?php
include 'db.php'; // Verbindung zur Datenbank herstellen

if (isset($_GET['shop_ID'])) {
    $shop_ID = intval($_GET['shop_ID']); // Sicherstellen, dass shop_ID eine Zahl ist

    // SQL-Abfrage
    $sql = "
        SELECT 
            npc.npc_ID, 
            CONCAT(npc_firstname.name, ' ', npc_lastname.name) AS fullname, 
            shop_employee.position AS role
        FROM shop_relations
        JOIN npc ON shop_relations.shop_employee_ID = npc.npc_ID
        JOIN npc_firstname ON npc.npc_firstname_ID = npc_firstname.npc_firstname_ID
        JOIN npc_lastname ON npc.npc_lastname_ID = npc_lastname.npc_lastname_ID
        JOIN shop_employee ON shop_relations.shop_employee_ID = shop_employee.shop_employee_ID
        WHERE shop_relations.shop_ID = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $shop_ID);
    $stmt->execute();
    $result = $stmt->get_result();

    $employees = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($employees);
} else {
    header('Content-Type: application/json');
    echo json_encode(["error" => "shop_ID is required"]);
}
?>
