<!DOCTYPE html>
<html lang="de-ch">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DnD Tool</title>
    <link rel="stylesheet" href="index.css">
    <script src="script.js" defer></script>
</head>
<body>
    <div class="header-buttons">
        <button onclick="switchView('generator')">Generator</button>
        <button onclick="switchView('npcList')">NPC List</button>
        <button onclick="switchView('quests')">Quests</button>
        <button onclick="switchView('main')">Main</button>
    </div>

    <div class="container" id="container-1">
        <div class="switch">
            <button onclick="loadCities()">Cities</button>
            <button onclick="loadVillages()">Villages</button>
        </div>
        <ul class="city-list" id="list-content"></ul>
    </div>

    <div class="container" id="container-2">
        <ul class="shop-type-list" id="shop-type-content"></ul>
    </div>

    <div class="container" id="container-3">
    <ul id="shop-list-content" class="shop-list"></ul>
</div>

    <div class="container" id="container-4">Active Quests</div>
    <div class="container" id="container-5">Map / Picture</div>
    <div class="container" id="container-6">Employees</div>
    <div class="container" id="container-7">Items</div>
    <div class="container" id="container-8">Stats</div>
    <div class="container" id="container-9">NPC Details</div>
</body>
</html>
