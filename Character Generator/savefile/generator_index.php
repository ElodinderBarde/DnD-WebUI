<?php
include '../db.php'; // Verbindung zur Datenbank herstellen

ini_set('log_errors', 1);
ini_set('error_log', '/htdocs/dnd_tool/Character Generator/to/error.log');
ini_set('display_errors', 0);
error_reporting(E_ALL);


if ($conn->connect_error) {
    die("Datenbankverbindung fehlgeschlagen: " . $conn->connect_error);
}

// Abrufen der Klassen-Daten
function getClasses()
{
    global $conn;
    $query = "SELECT npc_class_ID, name FROM npc_class";
    $result = $conn->query($query);
    $classes = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $classes[] = $row;
        }
    }
    return $classes;
}

$classes = getClasses();






//funktionierender Code ( npc Generieren, volk wählen)
// Funktion, um alle Rassen aus der Datenbank abzurufen
function getRaces()
{
    global $conn;
    $query = "SELECT race_ID, racename FROM race";
    $result = $conn->query($query);
    $races = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $races[] = $row;
        }
    }
    return $races;
}

// Abrufen der Rassen-Daten
$races = getRaces();
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NPC Generator</title>
    <link rel="stylesheet" href="generator.css">
    

</head>

<body>

    <div class="overlay">
        <h1>NPC Generator</h1>

        <!-- Tabellencontainer -->
        <div class="table-container">
            <h2>Generierte Daten</h2>
            <table id="generated-data">
                <thead>
                    <tr>
                        <th>Feld</th>
                        <th>Wert</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Generierte Daten werden hier eingefügt -->
                </tbody>
            </table>
        </div>

        <!-- Optionen -->
        <div class="options">
            <h2>Optionen</h2>
            <div class="checkbox-group">
                <label>
                    <input type="checkbox" id="allow-classes"> Klassen erlauben
                </label>
                <label>
                    <input type="checkbox" id="manual-classes"> Klassen manuell auswählen
                </label>
                <label>
                    <input type="checkbox" id="selectRaceCheckbox"> Volk manuell auswählen

                </label>

            </div>

            <!-- Dropdown für Klassen und Subklassen -->
            <div class="dropdowns">
                <label for="class-dropdown">Klasse:</label>
                <select id="class-dropdown" disabled>
                    <option value="">Wähle eine Klasse</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= htmlspecialchars($class['npc_class_ID']) ?>"><?= htmlspecialchars($class['name']) ?></option>
                    <?php endforeach; ?>
                </select>


                <label for="subclass-dropdown">Subklasse:</label>
                <select id="subclass-dropdown" disabled>
                    <option value="">Wähle eine Subklasse</option>
                    <!-- Dynamisch gefüllt -->
                </select>

                <div class="dropdowns">
                    <label for="raceDropdown">Volk:</label>
                    <select id="raceDropdown" name="race" disabled>
                        <option value="">-- Wählen Sie ein Volk --</option>
                        <?php foreach ($races as $race): ?>
                            <option value="<?= htmlspecialchars($race['race_ID']) ?>"><?= htmlspecialchars($race['racename']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Family Button -->
            <label for="generate-family-checkbox">
                <input type="checkbox" id="generate-family-checkbox"> Familie generieren
            </label>


            <!-- Bildcontainer -->
            <div class="image-container">
                <h2>Charakterbild</h2>
                <div id="npc-image">
                    <!-- Platzhalter für das Bild -->
                    <p>Bild wird hier angezeigt.</p>
                </div>
                <button id="regenerate-picture">Bild neu generieren</button>
            </div>

            <!-- Charakter erstellen -->
            <div class="character-actions">
                <button id="generate-character">Charakter erstellen</button>
            </div>



            <div id="main-npc-container" class="npc-container"></div>



            <!-- Familienliste -->
            <div id="family-container"></div>








            <!-- Zuweisung -->
            <div class="assignment">
                <h2>Charakter zuweisen</h2>
                <div class="assignment-dropdowns">
                    <label for="location-dropdown">Ort:</label>
                    <select id="location-dropdown">
                        <option value="">Wähle einen Ort</option>
                        <!-- Dynamisch gefüllt -->
                    </select>

                    <label for="shoptype-dropdown">Shoptyp:</label>
                    <select id="shoptype-dropdown">
                        <option value="">Wähle einen Shoptyp</option>
                        <!-- Dynamisch gefüllt -->
                    </select>

                    <label for="shop-dropdown">Shop:</label>
                    <select id="shop-dropdown">
                        <option value="">Wähle einen Shop</option>
                        <!-- Dynamisch gefüllt -->
                    </select>
                </div>

                <!-- Dropdown für Kunde -->
                <div id="customer-options">
                    <label for="customer-dropdown">Kunden-Typ:</label>
                    <select id="customer-dropdown">
                        <option value="">Wähle einen Kunden-Typ</option>
                    </select>
                </div>

                <div id="employee-options">
                    <label for="employee-dropdown">Mitarbeiter-Position:</label>
                    <select id="employee-dropdown">
                        <option value="">Wähle eine Mitarbeiter-Position</option>
                    </select>
                </div>


                <div class="role-checkboxes">
                    <label for="is-customer">Kunde</label>
                    <input type="checkbox" id="is-customer">

                    <label>
                        <input type="checkbox" id="is-employee"> Mitarbeiter
                    </label>
                </div>





                <!-- Speichern Button -->
                <button id="save-character">Charakter speichern</button>
            </div>
        </div>
        <script src="generator.js" defer></script>
        <script src="save_npc_data.js"></script>
        <script>
            document.getElementById("selectRaceCheckbox").addEventListener("change", function() {
                document.getElementById("raceDropdown").disabled = !this.checked; // Aktiviert oder deaktiviert das Dropdown
            });
        </script>
<script>document.getElementById("save-character").addEventListener("click", () => {
    console.log("Speichern-Button geklickt");
    // Dein Code, der die Anfrage sendet
});
document.getElementById("save-character").addEventListener("click", () => {
    const npcData = {
        shop_ID: document.getElementById("shop-dropdown").value,
        employee_ID: document.getElementById("employee-dropdown").value,
        customer_ID: document.getElementById("customer-dropdown").value,
        firstname_ID: 1, // Beispielwert
        lastname_ID: 1,  // Beispielwert
        familienName: "Familienname", // Beispielwert
        familienclan: "Familienclan", // Beispielwert
        position: "Position", // Beispielwert
        gender_ID: 1,  // Beispielwert
        age_ID: 25,   // Beispielwert
        race_ID: 1,   // Beispielwert
        class_ID: 1   // Beispielwert
    };

    fetch("save_character.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(npcData)
    })
    .then(response => response.json())
    .then(data => {
        console.log("Antwort vom Server:", data);
        alert(data.message);
    })
    .catch(error => {
        console.error("Fehler bei der Anfrage:", error);
        alert("Fehler beim Speichern.");
    });
});

</script>
<script>
        document.getElementById('loadNPC').addEventListener('click', () => {
            fetchNPCData(1); // Beispiel-NPC-ID
        });
    </script>
</body>

</html>