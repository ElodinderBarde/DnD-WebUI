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

    // Debugging: Überprüfen, ob die DOM-Elemente verfügbar sind
    console.log("DOM vollständig geladen:");
    console.log("customerCheckbox:", customerCheckbox);
    console.log("customerOptions:", customerOptions);
    console.log("employeeCheckbox:", employeeCheckbox);
    console.log("employeeOptions:", employeeOptions);

    // Überprüfen, ob alle relevanten Elemente vorhanden sind
    if (!customerCheckbox || !customerOptions || !customerDropdown) {
        console.error("Ein oder mehrere Elemente für Kunden-Typ fehlen.");
        return;
    }
    if (employeeCheckbox) {
        employeeCheckbox.addEventListener("change", () => {
            employeeOptions.style.display = employeeCheckbox.checked ? "block" : "none";
            if (employeeCheckbox.checked) {
                loadEmployeePositions();
            }
        });
    }

    // Orte laden
    function loadLocations() {
        fetch("get_cities.php")
            .then((response) => response.json())
            .then((cities) => {
                locationDropdown.innerHTML = '<option value="">Wähle einen Ort</option>';
                cities.forEach((city) => {
                    const option = document.createElement("option");
                    option.value = city.location_ID;
                    option.textContent = city.city_name;
                    locationDropdown.appendChild(option);
                });

                return fetch("get_villages.php");
            })
            .then((response) => response.json())
            .then((villages) => {
                villages.forEach((village) => {
                    const option = document.createElement("option");
                    option.value = village.location_ID;
                    option.textContent = village.village_name;
                    locationDropdown.appendChild(option);
                });
            })
            .catch((error) => console.error("Fehler beim Laden der Orte:", error));
    }

    // Shoptypen laden
    function loadShopTypes(locationID) {
        fetch(`get_shop_types.php?location_ID=${locationID}`)
            .then((response) => response.json())
            .then((shopTypes) => {
                shoptypeDropdown.innerHTML = '<option value="">Wähle einen Shoptyp</option>';
                shopTypes.forEach((type) => {
                    const option = document.createElement("option");
                    option.value = type.shop_type_name;
                    option.textContent = type.shop_type_name;
                    shoptypeDropdown.appendChild(option);
                });
            })
            .catch((error) => console.error("Fehler beim Laden der Shoptypen:", error));
    }

    // Shops laden
    function loadShops(locationID, shopTypeName) {
        fetch(`get_shops.php?location_ID=${locationID}&shop_type_name=${encodeURIComponent(shopTypeName)}`)
            .then((response) => response.json())
            .then((shops) => {
                shopDropdown.innerHTML = '<option value="">Wähle einen Shop</option>';
                shops.forEach((shop) => {
                    const option = document.createElement("option");
                    option.value = shop.shop_ID;
                    option.textContent = shop.shop_name;
                    shopDropdown.appendChild(option);
                });
            })
            .catch((error) => console.error("Fehler beim Laden der Shops:", error));
    }

    // Kunden-Typen laden
    function loadCustomerTypes() {
        fetch("get_customer_types.php")
            .then((response) => response.json())
            .then((data) => {
                customerDropdown.innerHTML = '<option value="">Wähle einen Kunden-Typ</option>';
                data.forEach((item) => {
                    const option = document.createElement("option");
                    option.value = item.id;
                    option.textContent = item.position;
                    customerDropdown.appendChild(option);
                });
            })
            .catch((error) => console.error("Fehler beim Laden der Kunden-Typen:", error));
    }

    // Mitarbeiter-Positionen laden
    function loadEmployeePositions() {
        fetch("get_employee_types.php")
            .then((response) => response.json())
            .then((data) => {
                employeeDropdown.innerHTML = '<option value="">Wähle eine Mitarbeiter-Position</option>';
                data.forEach((item) => {
                    const option = document.createElement("option");
                    option.value = item.shop_employee_ID;
                    option.textContent = item.position;
                    employeeDropdown.appendChild(option);
                });
            })
            .catch((error) => console.error("Fehler beim Laden der Mitarbeiter-Positionen:", error));
    }

    // Event-Listener hinzufügen
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

    // Initialisierung
    loadLocations();
});
