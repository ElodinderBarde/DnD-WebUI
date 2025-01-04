document.addEventListener("DOMContentLoaded", () => {
    // Initialisierung der Variablen
    const locationDropdown = document.getElementById("location-dropdown");
    const shoptypeDropdown = document.getElementById("shoptype-dropdown");
    const shopDropdown = document.getElementById("shop-dropdown");
    const saveButton = document.getElementById("save-character");
    const customerCheckbox = document.getElementById("is-customer");
    const customerOptions = document.getElementById("customer-options");
    const customerDropdown = document.getElementById("customer-dropdown");
    const employeeCheckbox = document.getElementById("is-employee");
    const employeeOptions = document.getElementById("employee-options");
    const employeeDropdown = document.getElementById("employee-dropdown");

    console.log("DOM vollständig geladen. Überprüfe DOM-Elemente:");
    console.log({
        locationDropdown,
        shoptypeDropdown,
        shopDropdown,
        saveButton,
        customerCheckbox,
        customerOptions,
        customerDropdown,
        employeeCheckbox,
        employeeOptions,
        employeeDropdown
    });

    // Prüfen auf notwendige DOM-Elemente
    if (!locationDropdown || !shoptypeDropdown || !shopDropdown || !saveButton) {
        console.error("Ein oder mehrere notwendige DOM-Elemente fehlen. Skript wird beendet.");
        return;
    }

    // Hilfsfunktionen zum Laden von Daten
    function loadData(url, callback) {
        fetch(url)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP-Fehler ${response.status}`);
                }
                return response.json();
            })
            .then(callback)
            .catch((error) => console.error(`Fehler beim Laden von ${url}:`, error));
    }

    function loadLocations() {
        console.log("Lade Städte und Dörfer...");
        loadData("get_cities.php", (cities) => {
            locationDropdown.innerHTML = '<option value="">Wähle einen Ort</option>';
            cities.forEach((city) => {
                const option = document.createElement("option");
                option.value = city.location_ID;
                option.textContent = city.city_name;
                locationDropdown.appendChild(option);
            });

            loadData("get_villages.php", (villages) => {
                villages.forEach((village) => {
                    const option = document.createElement("option");
                    option.value = village.location_ID;
                    option.textContent = village.village_name;
                    locationDropdown.appendChild(option);
                });
            });
        });
    }

    function loadShopTypes(locationID) {
        console.log(`Lade Shoptypen für Location ID: ${locationID}`);
        loadData(`get_shop_types.php?location_ID=${locationID}`, (shopTypes) => {
            shoptypeDropdown.innerHTML = '<option value="">Wähle einen Shoptyp</option>';
            shopTypes.forEach((type) => {
                const option = document.createElement("option");
                option.value = type.shop_type_name;
                option.textContent = type.shop_type_name;
                shoptypeDropdown.appendChild(option);
            });
        });
    }

    function loadShops(locationID, shopTypeName) {
        console.log(`Lade Shops für Location ID: ${locationID} und Shoptyp: ${shopTypeName}`);
        loadData(`get_shops.php?location_ID=${locationID}&shop_type_name=${encodeURIComponent(shopTypeName)}`, (shops) => {
            shopDropdown.innerHTML = '<option value="">Wähle einen Shop</option>';
            shops.forEach((shop) => {
                const option = document.createElement("option");
                option.value = shop.shop_ID;
                option.textContent = shop.shop_name;
                shopDropdown.appendChild(option);
            });
        });
    }

    function loadCustomerTypes() {
        console.log("Lade Kunden-Typen...");
        loadData("get_customer_types.php", (data) => {
            customerDropdown.innerHTML = '<option value="">Wähle einen Kunden-Typ</option>';
            data.forEach((item) => {
                const option = document.createElement("option");
                option.value = item.id;
                option.textContent = item.position;
                customerDropdown.appendChild(option);
            });
        });
    }

    function loadEmployeePositions() {
        console.log("Lade Mitarbeiter-Positionen...");
        loadData("get_employee_types.php", (data) => {
            employeeDropdown.innerHTML = '<option value="">Wähle eine Mitarbeiter-Position</option>';
            data.forEach((item) => {
                const option = document.createElement("option");
                option.value = item.shop_employee_ID;
                option.textContent = item.position;
                employeeDropdown.appendChild(option);
            });
        });
    }

    // Event-Listener
    locationDropdown.addEventListener("change", () => {
        const locationID = locationDropdown.value;
        if (locationID) {
            loadShopTypes(locationID);
            shopDropdown.innerHTML = '<option value="">Wähle einen Shop</option>';
        } else {
            shoptypeDropdown.innerHTML = '<option value="">Wähle einen Shoptyp</option>';
            shopDropdown.innerHTML = '<option value="">Wähle einen Shop</option>';
        }
    });

    shoptypeDropdown.addEventListener("change", () => {
        const locationID = locationDropdown.value;
        const shopTypeName = shoptypeDropdown.value;
        if (locationID && shopTypeName) {
            loadShops(locationID, shopTypeName);
        } else {
            shopDropdown.innerHTML = '<option value="">Wähle einen Shop</option>';
        }
    });

    customerCheckbox.addEventListener("change", () => {
        customerOptions.style.display = customerCheckbox.checked ? "block" : "none";
        if (customerCheckbox.checked) {
            loadCustomerTypes();
        }
    });

    employeeCheckbox.addEventListener("change", () => {
        employeeOptions.style.display = employeeCheckbox.checked ? "block" : "none";
        if (employeeCheckbox.checked) {
            loadEmployeePositions();
        }
    });

    // NPC speichern
    saveButton.addEventListener("click", () => {
        const npcData = {
            npc_fullname: {
                firstname: document.getElementById("firstname").value || "DefaultVorname",
                lastname: document.getElementById("lastname").value || "DefaultNachname"
            },
            shop_ID: document.getElementById("shop-dropdown").value || null,
            employee_ID: document.getElementById("employee-dropdown").value || null,
            customer_ID: document.getElementById("customer-dropdown").value || null,
            gender_ID: parseInt(document.getElementById("gender").value) || 1,
            age_ID: parseInt(document.getElementById("age").value) || null,
            race_ID: parseInt(document.getElementById("raceDropdown").value) || null,
            class_ID: parseInt(document.getElementById("class-dropdown").value) || null
        };

        console.log("Sende NPC-Daten:", npcData);

        fetch("http://localhost/dnd_tool/Character%20Generator/save_character.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(npcData)
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP-Fehler: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                console.log("Antwort vom Server:", data);
            })
            .catch((error) => console.error("Fehler beim Senden der NPC-Daten:", error));
    });

    // Initialisierung
    loadLocations();
});
