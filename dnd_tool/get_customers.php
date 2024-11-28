<?php
include 'db.php'; // Verbindung zur Datenbank herstellen

// Überprüfen, ob die shop_ID übergeben wurde
if (isset($_GET['shop_ID'])) {
    $shop_ID = intval($_GET['shop_ID']); // shop_ID als Integer sichern

    // SQL-Abfrage, um alle Mitarbeiter eines Shops abzurufen
    $sql = "
        SELECT npc.npc_ID, npc.name
        FROM shop_relations
        JOIN npc ON shop_relations.npc_ID = npc.npc_ID
        WHERE shop_relations.shop_ID = $shop_ID
          AND shop_relations.relation_type = 'Mitarbeiter'
    ";

    // Debugging: SQL-Abfrage anzeigen
    error_log("SQL Query: $sql");

    $result = $conn->query($sql);

    if (!$result) {
        // SQL-Fehler zurückgeben
        die(json_encode(["error" => "SQL Error: " . $conn->error]));
    }

    $employees = [];
    if ($result->num_rows > 0) {
        // Ergebnisse in ein Array speichern
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
    }

    // Debugging: Ergebnisse anzeigen
    error_log("Employees gefunden: " . json_encode($employees));

    // JSON-Ausgabe
    header('Content-Type: application/json');
    echo json_encode($employees);
} else {
    // Fehler, wenn shop_ID fehlt
    header('Content-Type: application/json');
    echo json_encode(["error" => "shop_ID is required"]);
}
?>
