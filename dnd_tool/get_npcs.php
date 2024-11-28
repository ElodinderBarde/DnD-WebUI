<?php
include 'db.php'; 

$sql = "
    SELECT
        npc.npc_ID,
        npc_firstname.first_name,
        npc_lastname.last_name,
        npc_gender.gender_name,
        npc_class.class_name
    FROM
        npc
    LEFT JOIN npc_firstname ON npc.npc_firstname_ID = npc_firstname.npc_firstname_ID
    LEFT JOIN npc_lastname ON npc.npc_lastname_ID = npc_lastname.npc_lastname_ID
    LEFT JOIN npc_gender ON npc.npc_gender_ID = npc_gender.npc_gender_ID
    LEFT JOIN npc_class ON npc.npc_class_ID = npc_class.npc_class_ID
";

$result = $conn->query($sql);

$npcs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $npcs[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($npcs);
?>
