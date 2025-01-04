<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $rawInput = file_get_contents('php://input');
    $inputData = json_decode($rawInput, true);

    // JSON-Validierung
    if (!$inputData) {
        throw new Exception("Ungültige oder leere JSON-Daten.");
    }

    writeNpcToCsv($inputData);

    echo json_encode(['message' => 'NPC erfolgreich zwischengespeichert!']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['message' => 'Fehler: ' . $e->getMessage()]);
}
$npcData = [
    'npc_fullname_ID' => $fullnameID,
    'gender_ID' => $inputData['npc_gender']['id'] ?? null,
    'age_ID' => $inputData['npc_age']['id'] ?? null,
    'race_ID' => $inputData['race']['id'] ?? null,
    'class_ID' => $inputData['npc_class']['id'] ?? null,
    'subclass_ID' => $inputData['npc_subclass']['id'] ?? null,
    'background_ID' => $inputData['npc_background']['id'] ?? null,
    'beardstyle_ID' => $inputData['npc_beardstyle']['id'] ?? null,
    'betonung_ID' => $inputData['npc_betonung']['id'] ?? null,
    'dislikes_ID' => $inputData['npc_dislikes']['id'] ?? null,
    'haircolor_ID' => $inputData['npc_haircolor']['id'] ?? null,
    'hairstyle_ID' => $inputData['npc_hairstyle']['id'] ?? null,
    'jackets_ID' => $inputData['npc_jackets']['id'] ?? null,
    'kleidungsqualität_ID' => $inputData['npc_kleidungsqualität']['id'] ?? null,
    'likes_ID' => $inputData['npc_likes']['id'] ?? null,
    'personality_ID' => $inputData['npc_personality']['id'] ?? null,
    'talkingstyle_ID' => $inputData['npc_talkingstyle']['id'] ?? null,
    'trousers_ID' => $inputData['npc_trousers']['id'] ?? null,
    'flaw_ID' => $inputData['npc_flaw']['id'] ?? null,
    'ideals_ID' => $inputData['npc_ideals']['id'] ?? null,
    'jewellery_ID' => $inputData['npc_jewellery']['id'] ?? null,
    'other_description_ID' => $inputData['npc_other_description']['id'] ?? null
];

function writeNpcToCsv($npcData, $filename = 'cache/npc_data.csv') {
    if (!file_exists(dirname($filename))) {
        mkdir(dirname($filename), 0777, true);
    }

    $file = fopen($filename, 'a');

    // Definierte Reihenfolge der Felder
    $fields = [
        $npcData['npc_fullname']['firstname'] ?? '',
        $npcData['npc_fullname']['lastname'] ?? '',
        $npcData['npc_age']['value'] ?? '',
        $npcData['race']['value'] ?? '',
        $npcData['npc_gender']['value'] ?? '',
        $npcData['npc_class']['value'] ?? '',
        $npcData['npc_subclass']['value'] ?? '',
        $npcData['npc_background']['value'] ?? '',
        $npcData['npc_personality']['value'] ?? '',
        $npcData['npc_likes']['value'] ?? '',
        $npcData['npc_dislikes']['value'] ?? '',
        $npcData['npc_haircolor']['value'] ?? '',
        $npcData['npc_hairstyle']['value'] ?? '',
        $npcData['npc_beardstyle']['value'] ?? null,
        $npcData['npc_jackets']['value'] ?? '',
        $npcData['npc_trousers']['value'] ?? '',
        $npcData['npc_kleidungsqualität']['value'] ?? '',
        $npcData['npc_flaw']['value'] ?? '',
        $npcData['npc_ideals']['value'] ?? '',
        $npcData['npc_jewellery']['value'] ?? '',
        $npcData['npc_other_description']['value'] ?? '',
        $npcData['npc_talkingstyle']['value'] ?? ''
    ];

    // Schreiben in die CSV-Datei
    fputcsv($file, $fields);
    fclose($file);
}
function writeFamilyToCsv($familyData, $filename = 'cache/family_data.csv') {
    foreach (['parents', 'children', 'grandparents', 'aunts_uncles'] as $role) {
        foreach ($familyData[$role] as $member) {
            writeNpcToCsv($member, $filename);
        }
    }
}

