<?php
include 'db.php'; // Verbindung zur Datenbank herstellen

// Überprüfen, ob die Shop-ID übergeben wurde
if (isset($_GET['shop_ID'])) {
    $shop_ID = intval($_GET['shop_ID']); // Shop-ID in eine Zahl umwandeln

    // SQL-Abfrage, um die Mitarbeiterdaten zu erhalten
    $sql = "
SELECT DISTINCT
    npc.npc_ID,
    npc_firstname.firstname AS first_name,
    npc_lastname.lastname AS last_name,
    npc.race_ID AS race_id,
    shop_employee.position AS role
FROM npc
LEFT JOIN shop_relations ON npc.shop_relations_ID = shop_relations.shop_relations_ID
LEFT JOIN shop ON shop_relations.shop_ID = shop.shop_ID
LEFT JOIN npc_fullname ON npc.npc_fullname_ID = npc_fullname.npc_fullname_ID
LEFT JOIN dnd.npc_firstname ON npc_fullname.npc_firstname_ID = dnd.npc_firstname.npc_firstname_ID
LEFT JOIN dnd.npc_lastname ON npc_fullname.npc_lastname_ID = dnd.npc_lastname.npc_lastname_ID
LEFT JOIN shop_employee ON shop_relations.shop_employee_ID = shop_employee.shop_employee_ID
WHERE shop.shop_ID = ?
  AND shop_employee.position IS NOT NULL;
;

    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        // Fehler in der SQL-Abfrage protokollieren
        echo json_encode(["error" => "SQL-Fehler: " . $conn->error]);
        exit;
    }

    // SQL-Abfrage ausführen
    $stmt->bind_param("i", $shop_ID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Ergebnisse sammeln
    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = [
            'npc_ID' => $row['npc_ID'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'race_id' => $row['race_id'],
            'role' => $row['role']
        ];
    }

    // JSON-Header setzen und Ergebnisse zurückgeben
    header('Content-Type: application/json');
    echo json_encode($employees);
    exit; // Beende das Skript, um unnötige Ausgaben zu vermeiden
} else {
    // Fehlermeldung, wenn keine Shop-ID übergeben wurde
    header('Content-Type: application/json');
    echo json_encode(["error" => "shop_ID is required"]);
    exit;
}
?>