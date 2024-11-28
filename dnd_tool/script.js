// Funktion, um die Button-Aktivierung zu verwalten
function setActiveButton(buttonId) {
    // Entferne die 'active'-Klasse von allen Buttons
    document.querySelectorAll('.switch button').forEach(button => {
        button.classList.remove('active');
    });
    // Füge die 'active'-Klasse zum geklickten Button hinzu
    const activeButton = document.getElementById(buttonId);
    if (activeButton) {
        activeButton.classList.add('active');
    }
}

// Funktion, um Städte zu laden
function loadCities() {
    setActiveButton('load-cities-button'); // Aktiviere den Cities-Button

    fetch('get_cities.php')
        .then(response => response.json())
        .then(data => {
            const list = document.getElementById('list-content');
            list.innerHTML = ''; // Alte Inhalte entfernen
            data.forEach(city => {
                const cityItem = document.createElement('li');
                cityItem.textContent = city.city_name;
                cityItem.className = 'city-item';
                cityItem.onclick = () => {
                    console.log(`City ID: ${city.city_ID}, Location ID: ${city.location_ID}`);
                    loadShopTypes(city.location_ID); // Shop Types basierend auf Location ID laden
                };
                list.appendChild(cityItem);
            });
        })
        .catch(error => console.error('Fehler beim Laden der Städte:', error));
}

// Funktion, um Dörfer zu laden
function loadVillages() {
    setActiveButton('load-villages-button'); // Aktiviere den Villages-Button

    fetch('get_villages.php')
        .then(response => response.json())
        .then(data => {
            const list = document.getElementById('list-content');
            list.innerHTML = ''; // Alte Inhalte entfernen
            data.forEach(village => {
                const villageItem = document.createElement('li');
                villageItem.textContent = village.village_name;
                villageItem.className = 'city-item';
                villageItem.onclick = () => {
                    console.log(`Village ID: ${village.location_ID}`);
                    loadShopTypes(village.location_ID); // Shop Types basierend auf Location ID laden
                };
                list.appendChild(villageItem);
            });
        })
        .catch(error => console.error('Fehler beim Laden der Dörfer:', error));
}
function loadShopTypes(locationID) {
    console.log("Lade Shop Types für Location ID:", locationID);

    fetch(`get_shop_types.php?location_ID=${locationID}`)
        .then(response => response.json())
        .then(data => {
            console.log("Shop Types erhalten:", data);
            const container = document.getElementById('shop-type-content');
            if (!container) {
                console.error("Das Element mit der ID 'shop-type-content' wurde nicht gefunden.");
                return;
            }
            container.innerHTML = ''; // Alte Inhalte entfernen

            if (data.length > 0) {
                data.forEach(type => {
                    console.log("Shop Type:", type.shop_type_name);
                    const listItem = document.createElement('li');
                    listItem.textContent = type.shop_type_name;
                    listItem.className = 'shop-type-item';
                    listItem.onclick = () => {
                        console.log(`Shop Type ausgewählt: ${type.shop_type_name}`);
                        // Übergabe des korrekten Shop-Type-Namens
                        loadShops(locationID, type.shop_type_name);
                    };
                    container.appendChild(listItem);
                });
            } else {
                container.innerHTML = '<p>Keine Shop Types gefunden</p>';
            }
        })
        .catch(error => console.error('Fehler beim Laden der Shop Types:', error));
}


function loadShops(locationID, shopTypeName) {
    console.log("Lade Shops für Location ID:", locationID, "und Shop Type:", shopTypeName);

    fetch(`get_shops.php?location_ID=${locationID}&shop_type_name=${encodeURIComponent(shopTypeName)}`)
        .then(response => {
            console.log("HTTP-Response:", response.status);
            if (!response.ok) {
                throw new Error(`HTTP-Fehler! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Shops erhalten:", data);
            const container = document.getElementById('shop-list-content');
            if (!container) {
                console.error("Das Element mit der ID 'shop-list-content' wurde nicht gefunden.");
                return;
            }
            container.innerHTML = ''; // Alte Inhalte entfernen

            if (data.length > 0) {
                data.forEach(shop => {
                    console.log("Shop:", shop);
                    const listItem = document.createElement('li');
                    // Nur den Shop-Namen anzeigen:
                    listItem.textContent = shop.shop_name;
                    listItem.className = 'shop-item';
                    container.appendChild(listItem);
                });
            } else {
                container.innerHTML = '<p>Keine Shops gefunden</p>';
            }
        })
        .catch(error => console.error('Fehler beim Laden der Shops:', error));
}


// Funktion aufrufen, wenn die Seite geladen wird
document.addEventListener('DOMContentLoaded', loadCities);
        