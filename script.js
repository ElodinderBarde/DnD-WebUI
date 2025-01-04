// Globale Variablen
let currentShopID = null; // Aktuell ausgewählter Shop
let currentView = "employees"; // Standardansicht

// Funktion, um aktive Buttons zu setzen
function setActiveView() {
    const employeeButton = document.querySelector('button[onclick="loadEmployees(currentShopID)"]');
    const customerButton = document.querySelector('button[onclick="loadCustomers(currentShopID)"]');
    const employeeList = document.getElementById('employee-content');
    const customerList = document.getElementById('customers-content');

    if (!employeeButton || !customerButton) {
        console.error("Buttons für Mitarbeiter oder Gäste nicht gefunden.");
        return;
    }

    if (currentView === "employees") {
        employeeButton.classList.add("active");
        customerButton.classList.remove("active");
        employeeList.style.display = 'block';
        customerList.style.display = 'none';
    } else {
        customerButton.classList.add("active");
        employeeButton.classList.remove("active");
        customerList.style.display = 'block';
        employeeList.style.display = 'none';
    }
}

// Städte laden
function loadCities() {
    console.log("loadCities: Start");
    fetch('http://localhost/dnd_tool/get_cities.php')
        .then(response => response.json())
        .then(data => {
            console.log("loadCities: Daten geladen", data);
            const list = document.getElementById('list-content');
            list.innerHTML = ''; // Alte Inhalte entfernen
            data.forEach(city => {
                const cityItem = document.createElement('li');
                cityItem.textContent = city.city_name;
                cityItem.className = 'city-item';
                cityItem.onclick = () => {
                    console.log(`City ausgewählt: ID=${city.city_ID}, Location ID=${city.location_ID}`);
                    loadShopTypes(city.location_ID); // Shop-Typen laden
                    loadCityMap(city.city_ID); // Stadtbild laden
                };
                list.appendChild(cityItem);
            });
        })
        .catch(error => console.error('Fehler beim Laden der Städte:', error));
}

// Dörfer laden
function loadVillages() {
    fetch('http://localhost/dnd_tool/get_villages.php')
        .then(response => response.json())
        .then(data => {
            const list = document.getElementById('list-content');
            list.innerHTML = '';
            data.forEach(village => {
                const villageItem = document.createElement('li');
                villageItem.textContent = village.village_name;
                villageItem.className = 'city-item';
                villageItem.onclick = () => {
                    loadShopTypes(village.location_ID);
                };
                list.appendChild(villageItem);
            });
        })
        .catch(error => console.error('Fehler beim Laden der Dörfer:', error));
}

// Shop-Typen laden
function loadShopTypes(locationID) {
    fetch(`http://localhost/dnd_tool/get_shop_types.php?location_ID=${locationID}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('shop-type-content');
            container.innerHTML = '';
            data.forEach(type => {
                const listItem = document.createElement('li');
                listItem.textContent = type.shop_type_name;
                listItem.className = 'shop-type-item';
                listItem.onclick = () => {
                    loadShops(locationID, type.shop_type_name);
                };
                container.appendChild(listItem);
            });
        })
        .catch(error => console.error('Fehler beim Laden der Shop-Typen:', error));
}

// Shops laden
function loadShops(locationID, shopTypeName) {
    fetch(`http://localhost/dnd_tool/get_shops.php?location_ID=${locationID}&shop_type_name=${encodeURIComponent(shopTypeName)}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('shop-list-content');
            container.innerHTML = '';
            data.forEach(shop => {
                const listItem = document.createElement('li');
                listItem.textContent = shop.shop_name;
                listItem.className = 'shop-item';
                listItem.onclick = () => {
                    currentShopID = shop.shop_ID;
                    loadEmployees(currentShopID);
                    loadItems(currentShopID);
                };
                container.appendChild(listItem);
            });
        })
        .catch(error => console.error('Fehler beim Laden der Shops:', error));
}

// Mitarbeiter laden
function loadEmployees(shopID) {
    currentView = "employees";
    setActiveView();
    fetch(`http://localhost/dnd_tool/get_employees.php?shop_ID=${shopID}`)
        .then(response => response.json())
        .then(data => {
            renderList(data, "employee-content", "role");
        })
        .catch(error => console.error('Fehler beim Laden der Mitarbeiter:', error));
}

// Gäste laden
function loadCustomers(shopID) {
    currentView = "customers";
    setActiveView();
    fetch(`http://localhost/dnd_tool/get_customers.php?shop_ID=${shopID}`)
        .then(response => response.json())
        .then(data => {
            renderList(data, "customers-content", "position");
        })
        .catch(error => console.error('Fehler beim Laden der Kunden:', error));
}

// Liste rendern
function renderList(data, containerId, roleKey) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    if (data.length > 0) {
        data.forEach(item => {
            const listItem = document.createElement('li');
            listItem.innerHTML = `
                <strong>${item.first_name} ${item.last_name}</strong><br>
                ${roleKey ? `<em>Position:</em> ${item[roleKey]}<br>` : ''}
            `;
            listItem.className = 'employee-item';
            listItem.onclick = () => {
                loadNpcDetails(item.npc_ID); // NPC-Details laden
            };
            container.appendChild(listItem);
        });
    } else {
        container.innerHTML = '<p>Keine Daten gefunden</p>';
    }
}

// Items laden
function loadItems(shopID) {
    fetch(`http://localhost/dnd_tool/get_items.php?shop_ID=${shopID}`)
        .then(response => response.json())
        .then(data => {
            renderItemList(data, "items-content");
        })
        .catch(error => console.error("Fehler beim Laden der Items:", error));
}

// Item-Liste rendern
function renderItemList(data, containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    if (data.length > 0) {
        data.forEach(item => {
            const listItem = document.createElement("li");
            listItem.className = "item-list-item";
            listItem.innerHTML = `
                <strong>${item.itemName}</strong><br>
                <em>Typ:</em> ${item.type}<br>
                <em>Preis:</em> ${item.special_price || item.base_price} Gold<br>
                <em>Menge:</em> ${item.quantity}
            `;
            container.appendChild(listItem);
        });
    } else {
        container.innerHTML = "<p>Keine Items verfügbar.</p>";
    }
}

// NPC-Details laden
function loadNpcDetails(npcID) {
    console.log(`loadNpcDetails: Start mit NPC ID=${npcID}`);
    fetch(`http://localhost/dnd_tool/get_npcs.php?npc_ID=${npcID}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP-Fehler! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("loadNpcDetails: Daten geladen", data);
            if (data.length > 0) {
                renderNpcData(data[0]); // Nimm den ersten (und einzigen) NPC aus der Liste
                loadNpcPicture(npcID); // NPC-Bild laden
            } else {
                console.error("Keine NPC-Daten gefunden.");
            }
        })
        .catch(error => console.error("Fehler beim Laden der NPC-Daten:", error));
}

// NPC-Daten anzeigen
function renderNpcData(npc) {
    console.log("renderNpcData: Start", npc);

    // Grundlegende Felder
    document.getElementById('npc-name').textContent = npc.npc_name || "Unbekannt";
    document.getElementById('npc_betonung').textContent = `Betonung: ${npc.npc_betonung || "Keine"}`;
    document.getElementById('npc_talkingstyle').textContent = `Spracheigenheit: ${npc.npc_talkingstyle || "Keine"}`;
    document.getElementById('npc_clan').textContent = `Gruppenzugehörigkeit: ${npc.npc_clan || "Keine"}`;

    // Biografie
    document.getElementById('npc_race').textContent = npc.npc_race || "Unbekannt";
    document.getElementById('npc_gender').textContent = npc.npc_gender || "Unbekannt";
    document.getElementById('npc_age').textContent = npc.npc_age || "Unbekannt";
    document.getElementById('npc_background').textContent = npc.npc_background || "Keine";
    document.getElementById('npc_status').textContent = "Aktiv";

    // Persönlichkeit
    document.getElementById('npc-personality').textContent = npc.npc_personality || "Keine Details";
    document.getElementById('npc-likes').textContent = npc.npc_likes || "Keine Vorlieben";
    document.getElementById('npc-dislikes').textContent = npc.npc_dislikes || "Keine Abneigungen";

    // Kleidung und Aussehen
    document.getElementById('npc_kleidungsqualitaet').textContent = npc.kleidungsqualitaet || "Keine";
    document.getElementById('npc_jackets').textContent = npc.oberteil || "Keine";
    document.getElementById('npc_trousers').textContent = npc.hose || "Keine";
    document.getElementById('npc_haircolor').textContent = npc.haircolor || "Keine";
    document.getElementById('npc_hairstyle').textContent = npc.hairstyle || "Keine";
    document.getElementById('npc_beardstyle').textContent = npc.beardstyle || "Keine";

    // Klasse und Subklasse
    document.getElementById('npc_class').textContent = npc.class || "Keine";
    document.getElementById('npc_subclass').textContent = npc.subclass || "Keine";

    // Stats
    document.getElementById('npc-str').textContent = `${npc.str || 10} (+${Math.floor((npc.str - 10) / 2)})`;
    document.getElementById('npc-dex').textContent = `${npc.dex || 10} (+${Math.floor((npc.dex - 10) / 2)})`;
    document.getElementById('npc-con').textContent = `${npc.con || 10} (+${Math.floor((npc.con - 10) / 2)})`;
    document.getElementById('npc-int').textContent = `${npc.int || 10} (+${Math.floor((npc.int - 10) / 2)})`;
    document.getElementById('npc-wis').textContent = `${npc.wis || 10} (+${Math.floor((npc.wis - 10) / 2)})`;
    document.getElementById('npc-cha').textContent = `${npc.cha || 10} (+${Math.floor((npc.cha - 10) / 2)})`;
}

// Initialisierung
document.addEventListener('DOMContentLoaded', () => {
    loadCities();

    const mapContainer = document.getElementById("container-5");
    const fullscreenContainer = document.getElementById("fullscreen-container");
    const fullscreenImage = document.getElementById("fullscreen-image");
    const closeButton = document.getElementById("close-fullscreen");

    // Zoom- und Drag-Variablen
    let scale = 1;
    let isDragging = false;
    let startX, startY, translateX = 0, translateY = 0;

    // Event-Listener für das Bild im `container-5`
    mapContainer.addEventListener("click", () => {
        const imgElement = mapContainer.querySelector("img"); // Bild im Container
        if (imgElement) {
            fullscreenImage.src = imgElement.src; // Bildquelle setzen
            fullscreenContainer.classList.add("visible"); // Vollbild anzeigen
        }
    });

    // Event-Listener für den Schließen-Button
    closeButton.addEventListener("click", () => {
        fullscreenContainer.classList.remove("visible"); // Vollbild ausblenden
        resetZoom(); // Zoom zurücksetzen
    });

    // Zoom-Funktionalität mit Mausrad
    fullscreenImage.addEventListener("wheel", (e) => {
        e.preventDefault();
        scale += e.deltaY * -0.001; // Zoomfaktor anpassen
        scale = Math.min(Math.max(1, scale), 5); // Begrenzen zwischen 1x und 5x Zoom
        fullscreenImage.style.transform = `scale(${scale}) translate(${translateX}px, ${translateY}px)`;
    });

    // Drag-Funktionalität starten
    fullscreenImage.addEventListener("mousedown", (e) => {
        isDragging = true;
        startX = e.clientX - translateX;
        startY = e.clientY - translateY;
        fullscreenImage.style.cursor = "grabbing";
    });

    // Dragging beenden
    fullscreenImage.addEventListener("mouseup", () => {
        isDragging = false;
        fullscreenImage.style.cursor = "grab";
    });

    // Dragging während Bewegung
    fullscreenImage.addEventListener("mousemove", (e) => {
        if (!isDragging) return;
        translateX = e.clientX - startX;
        translateY = e.clientY - startY;
        fullscreenImage.style.transform = `scale(${scale}) translate(${translateX}px, ${translateY}px)`;
    });

    // Zoom zurücksetzen
    function resetZoom() {
        scale = 1;
        translateX = 0;
        translateY = 0;
        fullscreenImage.style.transform = `scale(${scale}) translate(${translateX}px, ${translateY}px)`;
    }
});



function loadCityMap(cityID) {
    console.log(`loadCityMap: Start mit city_ID=${cityID}`);
    fetch(`http://localhost/dnd_tool/get_citymap.php?city_ID=${cityID}`)
        .then(response => response.json())
        .then(data => {
            if (data.citymap) {
                console.log("loadCityMap: Stadtbild geladen", data.citymap);
                const mapContainer = document.getElementById("container-5"); // ID des Containers für die Karte
                if (mapContainer) {
                    mapContainer.innerHTML = `<img src="${data.citymap}" alt="Stadtkarte" style="max-width: 100%; max-height: 100%;">`;
                } else {
                    console.error("Container für Stadtbild nicht gefunden.");
                }
            } else {
                console.error("Kein Stadtbild verfügbar:", data);
            }
        })
        .catch(error => console.error("Fehler beim Laden des Stadtbildes:", error));
}


function loadNpcPicture(npcID) {
    console.log(`Lade NPC-Bild mit ID=${npcID}`);

    // Anfrage an das Backend senden
    fetch(`http://localhost/dnd_tool/get_npc_picture.php?npc_ID=${npcID}`)
        .then(response => response.json())
        .then(data => {
            console.log("Bild-Daten geladen:", data);

            const npcPictureContainer = document.getElementById('container-10');
            if (!npcPictureContainer) {
                console.error("Container für NPC-Bild (#container-10) nicht gefunden.");
                return;
            }

            // Bild-URL überprüfen
            const imageUrl = data.picture;
            if (!imageUrl) {
                console.error("Keine Bild-URL im Backend zurückgegeben.");
                npcPictureContainer.innerHTML = "<p>Kein Bild verfügbar</p>";
                return;
            }

            // Bild im Container anzeigen
            npcPictureContainer.innerHTML = `<img src="${imageUrl}" alt="NPC Bild" id="npc-picture" style="width: 100%; height: auto; cursor: pointer;">`;

            // Fullscreen-Funktion für das Bild
            const npcPicture = document.getElementById('npc-picture');
            npcPicture.addEventListener('click', () => {
                const fullscreenContainer = document.getElementById('fullscreen-container');
                const fullscreenImage = document.getElementById('fullscreen-image');

                if (fullscreenContainer && fullscreenImage) {
                    fullscreenImage.src = imageUrl; // Setze das Bild-Quellattribut
                    fullscreenContainer.classList.add('visible'); // Zeige den Vollbild-Container an
                } else {
                    console.error('Fullscreen-Container oder Bild-Element nicht gefunden.');
                }
            });
        })
        .catch(error => console.error("Fehler beim Laden des NPC-Bildes:", error));
}
fetch("generator.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ action: "generate", race: selectedRace || null }),
})
.then(response => response.json())
.then(data => {
    console.log("Empfangene Daten:", data); // Prüfe, ob die korrekte Rasse enthalten ist
    renderGeneratedData(data);
})
.catch(error => console.error("Fehler beim Generieren:", error));
