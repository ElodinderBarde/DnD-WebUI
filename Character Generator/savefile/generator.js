document.addEventListener("DOMContentLoaded", () => {
    const allowClassesCheckbox = document.getElementById("allow-classes");
    const manualClassesCheckbox = document.getElementById("manual-classes");
    const classDropdown = document.getElementById("class-dropdown");
    const subclassDropdown = document.getElementById("subclass-dropdown");
    const generateFamilyCheckbox = document.getElementById("generate-family-checkbox");

let currentMainNPC = null; //Globale variable zum speichern des aktuellen Haupt NPC

    // Charakter generieren
    document.getElementById("generate-character").addEventListener("click", () => {
        const requestData = {
            action: "generate",
            race: document.getElementById("raceDropdown").value || null,
            allow_classes: allowClassesCheckbox.checked,
            manual_class: manualClassesCheckbox.checked ? classDropdown.value : null,
            manual_subclass: manualClassesCheckbox.checked ? subclassDropdown.value : null,
        };

        fetch("generator.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(requestData),
        })
            .then((response) => response.json())
            .then((npc) => {
                console.log("Einzelner Charakter generiert:", npc);
                renderGeneratedData(npc);

                // Familie generieren, falls die Checkbox aktiviert ist
                if (generateFamilyCheckbox && generateFamilyCheckbox.checked) {
                    generateFamilyWithNPC(npc);
                }
            })
            .catch((error) => console.error("Fehler bei der Charaktergenerierung:", error));
    });

    function generateFamilyWithNPC(npc) {
        const raceID = npc.race.id; // Gleiche Rasse wie der generierte NPC
        const lastname = npc.npc_fullname.lastname; // Nachname des Haupt-NPC
        const allowClasses = allowClassesCheckbox.checked;
    
        console.log("Haupt-NPC:", npc);

        fetch("get_families.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                generate_family: true,
                race: raceID,
                lastname: lastname, // Übergabe des Nachnamens
                allow_classes: allowClasses,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                console.log("Familie generiert:", data.family);
    
                // Familie rendern
                console.log("Übergebener Haupt-NPC:", npc);

                renderFamily(data.family, npc);
    
                // Haupt-NPC separat anzeigen
                renderMainNPC(npc);
            })
            .catch((error) => console.error("Fehler bei der Familiengenerierung:", error));
    }
    
    // Haupt-NPC separat rendern
    function renderMainNPC(npc) {
        if (!npc) {
            console.error("Fehler: Kein NPC-Datenobjekt übergeben.");
            return;
        }
    
        const mainNPCContainer = document.getElementById("main-npc-container");
        if (!mainNPCContainer) {
            console.error("Fehler: Der Container 'main-npc-container' wurde nicht gefunden.");
            return;
        }
        renderGeneratedData(npc);
        console.log("Haupt-NPC anzeigen:", npc);
    
        // Inhalt aktualisieren
        mainNPCContainer.innerHTML = `
            <h3>Haupt-NPC</h3>
            <div>
                <strong>Name:</strong> ${npc.npc_fullname ? npc.npc_fullname.value : `${npc.firstname} ${npc.lastname}`}<br>
                <strong>Alter:</strong> ${npc.npc_age?.value || "Unbekannt"}<br>
                <strong>Rolle:</strong> ${npc.role || "Unbekannt"}<br>
            </div>
        `;
    
        // Sicherstellen, dass der Container sichtbar ist
        mainNPCContainer.style.display = "block";
    
        // Debugging
        console.log("Container-Inhalt aktualisiert:", mainNPCContainer.innerHTML);
    }
    
    
    
    
    
    

    

    

    // Liste der Familienmitglieder rendern
    function renderFamily(family, npc) {
        const familyContainer = document.getElementById("family-container");
        familyContainer.innerHTML = ""; // Reset
    
        for (const [role, members] of Object.entries(family)) {
            const section = document.createElement("div");
            section.innerHTML = `<h3>${role.charAt(0).toUpperCase() + role.slice(1)}</h3>`;
    
            members.forEach((member, index) => {
                const memberDiv = document.createElement("div");
                memberDiv.innerHTML = `
                    <button class="npc-button" data-role="${role}" data-index="${index}">
                        ${member.firstname} ${member.lastname} (${member.age} Jahre alt, ${member.role})
                    </button>`;
                section.appendChild(memberDiv);
    
                memberDiv.querySelector(".npc-button").addEventListener("click", () => {
                    showNPCDetails(role, index, family);
                });
            });
    
            familyContainer.appendChild(section);
        }
    
        // Haupt-NPC-Button hinzufügen
        const mainNPCButton = document.createElement("button");
        mainNPCButton.textContent = "Haupt-NPC anzeigen";
        mainNPCButton.addEventListener("click", () => {
            console.log("Haupt-NPC-Button wurde geklickt:", npc);
            renderMainNPC(npc);
        });
        familyContainer.appendChild(mainNPCButton);
    }
    
    
    
    
    // NPC-Details anzeigen
    function showNPCDetails(role, index, family) {
        const member = family[role][index]; // Familienmitglied basierend auf Rolle und Index
    
        const tableBody = document.getElementById("generated-data").querySelector("tbody");
        tableBody.innerHTML = ""; // Reset
    
        Object.entries(member).forEach(([key, value]) => {
            const row = document.createElement("tr");
            if (typeof value === "object" && value !== null) {
                value = value.name || value.description || value.racename || value.npc_gender || value.role || "[Unbekannt]";
            } else if (value === null || value === undefined) {
                value = "N/A";
            }
    
            row.innerHTML = `
                <td>${key}</td>
                <td>${value}</td>
            `;
            tableBody.appendChild(row);
        });
    }
    
    

    // NPC-Details anzeigen
    function showNPCDetails(role, index, family) {
        const member = family[role][index];

        const tableBody = document.getElementById("generated-data").querySelector("tbody");
        tableBody.innerHTML = ""; // Reset

        Object.entries(member).forEach(([key, value]) => {
            const row = document.createElement("tr");
            if (typeof value === "object" && value !== null) {
                value = value.name || value.description || value.racename || value.npc_gender || value.role || "[Unbekannt]";
            } else if (value === null || value === undefined) {
                value = "N/A";
            }

            row.innerHTML = `
                <td>${key}</td>
                <td>${value}</td>
            `;
            tableBody.appendChild(row);
        });
    }

    // Tabelle für einen Charakter rendern
    function renderGeneratedData(data) {
        const tableBody = document.getElementById("generated-data").querySelector("tbody");
        tableBody.innerHTML = ""; // Reset

        Object.keys(data).forEach((key) => {
            const row = document.createElement("tr");
            let value = data[key];

            if (value && typeof value === "object") {
                value = value.value || "[Unbekannt]";
            } else if (value === null || value === undefined) {
                value = "N/A";
            }

            row.innerHTML = `
                <td>${key}</td>
                <td>${value}</td>
            `;
            tableBody.appendChild(row);
        });
    }

    // Dropdown aktivieren oder deaktivieren
    manualClassesCheckbox.addEventListener("change", function () {
        classDropdown.disabled = !this.checked;
        subclassDropdown.disabled = !this.checked;
    });

    // Klassen und Subklassen laden
    fetch("get_classes.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action: "get_classes" }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.classes) {
                classDropdown.innerHTML = '<option value="">Wähle eine Klasse</option>';
                data.classes.forEach((classItem) => {
                    const option = document.createElement("option");
                    option.value = classItem.id;
                    option.textContent = classItem.value;
                    classDropdown.appendChild(option);
                });
            }
        })
        .catch((error) => console.error("Fehler beim Laden der Klassen:", error));

    classDropdown.addEventListener("change", () => {
        const classID = classDropdown.value;
        fetch("get_subclasses.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "get_subclasses", class_id: classID }),
        })
            .then((response) => response.json())
            .then((data) => {
                subclassDropdown.innerHTML = '<option value="">Wähle eine Subklasse</option>';
                if (data.subclasses) {
                    data.subclasses.forEach((subclass) => {
                        const option = document.createElement("option");
                        option.value = subclass.id;
                        option.textContent = subclass.value;
                        subclassDropdown.appendChild(option);
                    });
                }
            })
            .catch((error) => console.error("Fehler beim Laden der Subklassen:", error));
    });
});
