<?php
include 'db.php'; 

// Überprüfen, ob eine NPC-ID übergeben wurde
$npcID = isset($_GET['npc_ID']) ? intval($_GET['npc_ID']) : 0;

if ($npcID === 0) {
    echo json_encode(["error" => "Keine gültige npc_ID angegeben."]);
    exit;
}

// SQL-Abfrage mit NPC-Filter
$sql = "
SELECT 
       CONCAT(firstname.firstname, ' ', lastname.lastname) AS npc_name,
       betonung.betonung AS npc_betonung,
       talkingstyle.description AS npc_talkingstyle,
       clan.clanname AS npc_clan,
       race.racename AS npc_race,
       npc_age.age AS npc_age,
       gender.npc_gender AS npc_gender,
       background.name AS npc_background,
       personality.description AS npc_personality,
       likes.description AS npc_likes,
       dislikes.description AS npc_dislikes,
       kleidungsqualitaet.description AS kleidungsqualitaet,
       jackets.name AS oberteil,
       trousers.name AS hose,
       haircolor.name AS haircolor,
       hairstyle.name AS hairstyle,
       beardstyle.name AS beardstyle,
       class.name AS class,
       subclass.name AS subclass
FROM npc
LEFT JOIN npc_class class ON npc.npc_class_ID = class.npc_class_ID
LEFT JOIN npc_subclass subclass ON npc.npc_subclass_ID = subclass.npc_subclass_ID
LEFT JOIN npc_fullname fullname ON npc.npc_fullname_ID = fullname.npc_fullname_ID
LEFT JOIN dnd.npc_firstname firstname ON fullname.npc_firstname_ID = firstname.npc_firstname_ID
LEFT JOIN dnd.npc_lastname lastname ON fullname.npc_lastname_ID = lastname.npc_lastname_ID
LEFT JOIN npc_betonung betonung ON npc.npc_betonung_ID = betonung.npc_betonung_ID
LEFT JOIN npc_talkingstyle talkingstyle ON npc.npc_talkingstyle_ID = talkingstyle.npc_talkingstyle_ID
LEFT JOIN npc_clan clan ON npc.clan_ID = clan.clan_ID
LEFT JOIN npc_age ON npc.npc_age_ID = npc.npc_age_ID
LEFT JOIN race ON npc.race_ID = race.race_ID 
LEFT JOIN npc_gender gender ON npc.npc_gender_ID = gender.npc_gender_ID
LEFT JOIN npc_background background ON npc.npc_background_ID = background.npc_background_ID
LEFT JOIN npc_personality personality ON npc.npc_personality_ID = personality.npc_personality_ID
LEFT JOIN npc_likes likes ON npc.npc_likes_ID = likes.npc_likes_ID
LEFT JOIN npc_dislikes dislikes ON npc.npc_dislikes_ID = dislikes.npc_dislikes_ID
LEFT JOIN npc_kleidungsqualität kleidungsqualitaet ON npc.npc_kleidungsqualität_ID = kleidungsqualitaet.npc_kleidungsqualität_ID
LEFT JOIN npc_jackets jackets ON npc.npc_jackets_ID = jackets.npc_jackets_ID
LEFT JOIN npc_trousers trousers ON npc.npc_trousers_ID = trousers.npc_trousers_ID 
LEFT JOIN npc_haircolor haircolor ON npc.npc_haircolor_ID = haircolor.npc_haircolor_ID
LEFT JOIN npc_hairstyle hairstyle ON npc.npc_hairstyle_ID = hairstyle.npc_hairstyle_ID
LEFT JOIN npc_beardstyle beardstyle ON npc.npc_beardstyle_ID = beardstyle.npc_beardstyle_ID
WHERE npc.npc_ID = $npcID;

";

$result = $conn->query($sql);

$npcs = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $npcs[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($npcs);
?>
