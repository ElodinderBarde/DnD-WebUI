
// Funktion, um aktive Buttons zu setzen
export function setActiveView() {
    const employeeButton = document.querySelector('button[onclick="loadEmployees(currentShopID)"]');
    const customerButton = document.querySelector('button[onclick="loadCustomers(currentShopID)"]');
    const employeeList = document.getElementById('employee-content');
    const customerList = document.getElementById('customers-content');

    console.log("setActiveView: Employee Button:", employeeButton);
    console.log("setActiveView: Customer Button:", customerButton);

    if (!employeeButton || !customerButton) {
        console.error("Buttons für Mitarbeiter oder Gäste nicht gefunden.");
        return; // Funktion abbrechen, wenn Buttons fehlen
    }

    // Umschalten der aktiven Buttons
    if (currentView === "employees") {
        employeeButton.classList.add("active");
        customerButton.classList.remove("active");
        // Mitarbeiterliste anzeigen, Gästeliste ausblenden
        employeeList.style.display = 'block';
        customerList.style.display = 'none';
    } else {
        customerButton.classList.add("active");
        employeeButton.classList.remove("active");
        // Gästeliste anzeigen, Mitarbeiterliste ausblenden
        customerList.style.display = 'block';
        employeeList.style.display = 'none';
    }

    console.log("setActiveView: Button-Klassen nach Update:", {
        employeeButton: employeeButton.className,
        customerButton: customerButton.className,
    });
    console.log("setActiveView: Sichtbarkeit der Listen - Mitarbeiter:", employeeList.style.display, "Gäste:", customerList.style.display);
}


// Städte laden
export function loadCities() {
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
                    loadShopTypes(city.location_ID);
                };
                list.appendChild(cityItem);
            });
        })
        .catch(error => console.error('Fehler beim Laden der Städte:', error));
}

// Dörfer laden
export function loadVillages() {
    console.log("loadVillages: Start");
    fetch('http://localhost/dnd_tool/get_villages.php')
        .then(response => response.json())
        .then(data => {
            console.log("loadVillages: Daten geladen", data);
            const list = document.getElementById('list-content');
            list.innerHTML = '';
            data.forEach(village => {
                const villageItem = document.createElement('li');
                villageItem.textContent = village.village_name;
                villageItem.className = 'city-item';
                villageItem.onclick = () => {
                    console.log(`Village ausgewählt: Location ID=${village.location_ID}`);
                    loadShopTypes(village.location_ID);
                };
                list.appendChild(villageItem);
            });
        })
        .catch(error => console.error('Fehler beim Laden der Dörfer:', error));
}

// Shop-Typen laden
export function loadShopTypes(locationID) {
    console.log(`loadShopTypes: Start mit Location ID=${locationID}`);
    fetch(`http://localhost/dnd_tool/get_shop_types.php?location_ID=${locationID}`)
        .then(response => response.json())
        .then(data => {
            console.log("loadShopTypes: Daten geladen", data);
            const container = document.getElementById('shop-type-content');
            container.innerHTML = '';
            data.forEach(type => {
                const listItem = document.createElement('li');
                listItem.textContent = type.shop_type_name;
                listItem.className = 'shop-type-item';
                listItem.onclick = () => {
                    console.log(`Shop Type ausgewählt: ${type.shop_type_name}`);
                    loadShops(locationID, type.shop_type_name);
                };
                container.appendChild(listItem);
            });
        })
        .catch(error => console.error('Fehler beim Laden der Shop-Typen:', error));
}

// Shops laden
export function loadShops(locationID, shopTypeName) {
    console.log(`loadShops: Start mit Location ID=${locationID}, Shop Type=${shopTypeName}`);
    fetch(`http://localhost/dnd_tool/get_shops.php?location_ID=${locationID}&shop_type_name=${encodeURIComponent(shopTypeName)}`)
        .then(response => response.json())
        .then(data => {
            console.log("loadShops: Daten geladen", data);
            const container = document.getElementById('shop-list-content');
            container.innerHTML = '';
            data.forEach(shop => {
                const listItem = document.createElement('li');
                listItem.textContent = shop.shop_name;
                listItem.className = 'shop-item';
                listItem.onclick = () => {
                    console.log(`Shop ausgewählt: Name=${shop.shop_name}, ID=${shop.shop_ID}`);
                    currentShopID = shop.shop_ID; // Shop-ID speichern
                    loadEmployees(currentShopID); // Standardansicht
                    loadItems(currentShopID); // Items automatisch laden

                    displayButtons(); // Buttons anzeigen
                };
                container.appendChild(listItem);
            });
        })
        .catch(error => console.error('Fehler beim Laden der Shops:', error));
}

// Mitarbeiter laden
export function loadEmployees(shopID, currentView) {
  console.log(`loadEmployees: Start mit Shop ID=${shopID}`);
  if (!currentView) {
      console.error("loadEmployees: currentView ist undefined");
      return;
  }
  setActiveView(currentView);
  fetch(`http://localhost/dnd_tool/get_employees.php?shop_ID=${shopID}`)
      .then(response => response.json())
      .then(data => {
          console.log("loadEmployees: Daten geladen", data);
          renderList(data, "employee-content", "role");
      })
      .catch(error => console.error('Fehler beim Laden der Mitarbeiter:', error));
}


// Gäste laden
export function loadCustomers(shopID) {
    console.log(`loadCustomers: Start mit Shop ID=${shopID}`);
    currentView = "customers";
    setActiveView();
    fetch(`http://localhost/dnd_tool/get_customers.php?shop_ID=${shopID}`)
        .then(response => response.json())
        .then(data => {
            console.log("loadCustomers: Daten geladen", data);
            renderList(data, "customers-content", "position");
        })
        .catch(error => console.error('Fehler beim Laden der Kunden:', error));
}

// Liste rendern
export function renderList(data, containerId, roleKey) {
    console.log("renderList: Start", { data, containerId, roleKey });

    const container = document.getElementById(containerId);
    if (!container) {
        console.error(`renderList: Element mit ID ${containerId} nicht gefunden.`);
        console.log("Aktuelle DOM-Struktur:", document.body.innerHTML); // DOM-Inhalt anzeigen
        return; // Abbrechen, wenn das Element nicht existiert
    }

    container.innerHTML = ''; // Alte Inhalte entfernen
    if (data.length > 0) {
        data.forEach(item => {
            const listItem = document.createElement('li');
            listItem.innerHTML = `
                <strong>${item.first_name} ${item.last_name}</strong><br>
                ${roleKey ? `<em>Position:</em> ${item[roleKey]}<br>` : ''}
            `;
            listItem.className = 'employee-item';
            container.appendChild(listItem);
        });
    } else {
        container.innerHTML = '<p>Keine Daten gefunden</p>';
    }
}


// Buttons sichtbar machen
export function displayButtons() {
    console.log("displayButtons: Start");
    const container = document.getElementById('container-6');
    const buttons = container.querySelector('.switch');
    if (buttons) {
        buttons.style.display = 'block'; // Buttons einblenden
    }
}


export function loadItems(shopID) {
    console.log(`loadItems: Start mit Shop ID=${shopID}`);
    fetch(`http://localhost/dnd_tool/get_items.php?shop_ID=${shopID}`)
        .then(response => response.json())
        .then(data => {
            console.log("loadItems: Daten geladen", data);
            renderItemList(data, "items-content");
        })
        .catch(error => console.error("Fehler beim Laden der Items:", error));
}


export function renderItemList(data, containerId) {
    console.log("renderItemList: Start", { data, containerId });

    const container = document.getElementById(containerId);
    if (!container) {
        console.error(`renderItemList: Element mit ID ${containerId} nicht gefunden.`);
        return;
    }

    container.innerHTML = ""; // Alte Inhalte entfernen

    if (data.length > 0) {
        data.forEach(item => {
            const listItem = document.createElement("li");
            listItem.className = "item-list-item";
            listItem.innerHTML = `
                <strong>${item.itemName}</strong><br>
                <em>Typ:</em> ${item.type}<br>
                <em>Preis:</em> ${item.special_price ? item.special_price : item.base_price} Gold<br>
                <em>Rabatt:</em> ${item.discount ? item.discount + "%" : "Kein Rabatt"}<br>
                <em>Menge:</em> ${item.quantity}
            `;
            container.appendChild(listItem);
        });
    } else {
        container.innerHTML = "<p>Keine Items verfügbar.</p>";
    }
}
