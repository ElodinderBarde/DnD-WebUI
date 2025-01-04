<?php
include 'db.php';

$npcID = $_GET['npc_ID'];
$stmt = $conn->prepare("SELECT picture FROM npc_picture WHERE npc_picture_ID = ?");
$stmt->bind_param("i", $npcID);
$stmt->execute();
$stmt->bind_result($npcPicture);
$stmt->fetch();

echo json_encode(["picture" => $npcPicture]);
?>
