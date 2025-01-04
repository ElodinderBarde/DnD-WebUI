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
    <!-- Buttons oben links -->
    <div class="header-buttons">
        <button onclick="switchView('generator')">Generator</button>
        <button onclick="switchView('npcList')">NPC List</button>
        <button onclick="switchView('quests')">Quests</button>
        <button onclick="switchView('main')">Main</button>
    </div>

    <!-- Hauptcontainer für Städte -->
    <div class="container" id="container-1">
        <div class="switch">
            <button onclick="loadCities()">Cities</button>
            <button onclick="loadVillages()">Villages</button>
        </div>
        <ul id="list-content" class="city-list"></ul> <!-- Liste für Städte und Dörfer -->
    </div>

    <!-- Container für Shop-Typen -->
    <div class="container" id="container-2">
        <ul id="shop-type-content" class="shop-type-list"></ul>
    </div>

    <!-- Container für Shops -->
    <div class="container" id="container-3">
        <ul id="shop-list-content" class="shop-list"></ul>
    </div>

    <!-- Container für Shops -->
    <div class="container" id="container-4">

    </div>



    <!-- Container für die Karte -->
    <div class="container" id="container-5">Map / Picture</div>
    <div id="fullscreen-container" class="hidden">
    <img id="fullscreen-image" src="" alt="Vollbild">
    <button id="close-fullscreen">X</button>
</div>

   <!-- Container für Mitarbeiter und Gäste -->
<div class="container" id="container-6">
    <div class="switch" style="display: block;">
        <button onclick="loadEmployees(currentShopID)" class="">Mitarbeiter</button>
        <button onclick="loadCustomers(currentShopID)" class="active">Gäste</button>
    </div>
    <ul id="employee-content" class="employee-list">
        <!-- Dynamische Inhalte für Mitarbeiter -->
    </ul>
    <ul id="customers-content" class="employee-list">
        <!-- Dynamische Inhalte für Gäste -->
    </ul>
</div>

    <!-- Weitere Container -->
    <div class="container" id="container-7">
    <ul id="items-content" class="item-list">
        <!-- Dynamische Inhalte für Items werden hier eingefügt -->
    </ul>
</div>
    <div class="container" id="container-8">
        
    <!-- Werte -->
    <div class="npc-section">
        <h2>Werte</h2>
        <table class="npc-stats">
            <thead>
                <tr>    
                <th>STR</th>
                <br>
                <td id="npc-str">10 (+0)</td>
                </tr>

                <tr>
                  <th>GES</th>
                 <td id="npc-dex">10 (+0)</td>
                </tr>

                <TR>    
                  <th>KON</th>
                  <td id="npc-con">10 (+0)</td>
                </TR>


                <tr>
                   <th>INT</th>
                    <td id="npc-int">10 (+0)</td>
                </tr>

                <TR>
                <th>WEI</th>
                <td id="npc-wis">10 (+0)</td>
                </TR>

                
                <tr>  
                    <th>CHA</th>
                    <td id="npc-cha">10 (+0)</td>
                </tr>

                <tr>
                    <th>Klasse</th>
                    <td id="npc_class">Test</td> </td>
                </tr>

                <tr>
                    <th>Subclass</th>
                    <td id="npc_subclass">Test </td>
                </tr>



            </thead>
            <tbody>
               
            </tbody>
        </table>
    </div>
    </div>
    <div class="container" id="container-9">
     
    <!-- NPC Name -->
    <div class="npc-header">
    <h1 id="npc-name">Vorname Nachname</h1>
    <p id="npc_betonung">Betonung: test</p>
    <p id="npc_talkingstyle">Spracheigenheit: test</p>
    <p id="npc_clan">Gruppenzugehörigkeit: test</p>
</div>

<div class="npc-section">
    <div class="npc-biography">
        <table>
            <tr><th>Volk</th><td id="npc_race">test</td></tr> 
            <tr><th>Gender</th><td id="npc_gender">test</td></tr>
            <tr><th>Alter</th><td id="npc_age">test</td></tr>
            <tr><th>Hintergrund</th><td id="npc_background">test</td></tr>
            <tr><th>Status</th><td id="npc_status">test</td></tr>
        </table>
    </div>
</div>

<div class="npc-section">
    <h1>Persönlichkeit</h1>
    <p id="npc-personality">Test</p>
</div>

<div class="npc-section">
    <p><strong>Mag:</strong> <span id="npc-likes">Test</span></p>
    <p><strong>Mag nicht:</strong> <span id="npc-dislikes">Test</span></p>
</div>

<table>
<tr><th>Kleidungsqualität</th><td id="npc_kleidungsqualitaet">Test</td></tr>
    <tr><th>Oberteil</th><td id="npc_jackets">test</td></tr>
    <tr><th>Hose</th><td id="npc_trousers">test</td></tr>
    <tr><th>Haarfarbe</th><td id="npc_haircolor">test</td></tr>
    <tr><th>Haarstil</th><td id="npc_hairstyle">test</td></tr>
    <tr><th>Bartstil</th><td id="npc_beardstyle">test</td></tr>
</table>
</div>


    <!-- Debugging und Initialisierung -->
    <script>
        function switchView(view) {
            console.log('Switching view to:', view);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const testElement = document.getElementById('employee-content');
            if (!testElement) {
                console.error("Fehler: #employee-content nicht im DOM gefunden.");
            } else {
                console.log("Element gefunden:", testElement);
            }
        });
    </script>
        <!-- Container für npc bild -->
        <div class="container" id="container-10">
    <p id="npc-picture-placeholder">Kein Bild verfügbar</p>
</div>

</body>
</html>
