<?php
include '../db.php'; // Verbindung zur Datenbank
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputData = json_decode(file_get_contents('php://input'), true);

    try {
        // Vor- und Nachnamen speichern oder IDs holen
        $firstnameID = getOrInsertFirstname($conn, $inputData['npc_fullname']['firstname']);
        $lastnameID = getOrInsertLastname($conn, $inputData['npc_fullname']['lastname']);

        // Debugging: Überprüfe die ermittelten IDs
        error_log("Firstname ID: $firstnameID");
        error_log("Lastname ID: $lastnameID");

        // Vollständigen Namen speichern oder ID holen
        $fullnameID = getOrInsertFullname($conn, $firstnameID, $lastnameID);

        // Debugging: Überprüfe die fullname ID
        error_log("Generated Fullname ID: $fullnameID");

        echo json_encode(['message' => 'NPC fullname erfolgreich gespeichert!', 'npc_fullname_ID' => $fullnameID]);
    } catch (Exception $e) {
        error_log("Fehler: " . $e->getMessage());
        echo json_encode(['message' => 'Fehler: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Nur POST-Anfragen erlaubt.']);
}
if (!isset($inputData['npc_fullname']) || !is_array($inputData['npc_fullname'])) {
  error_log("npc_fullname fehlt oder ist kein gültiges Array.");
  throw new Exception("npc_fullname fehlt oder ist kein gültiges Array.");
}

$firstname = $inputData['npc_fullname']['firstname'] ?? null;
$lastname = $inputData['npc_fullname']['lastname'] ?? null;

if (!$firstname || !$lastname) {
  error_log("Vorname oder Nachname fehlt in npc_fullname.");
  throw new Exception("Vorname oder Nachname fehlt in npc_fullname.");
}

// Funktion, um Vorname zu speichern oder ID zu holen
function getOrInsertFirstname($conn, $firstname) {
    $stmt = $conn->prepare("SELECT npc_firstname_ID FROM npc_firstname WHERE firstname = ?");
    $stmt->bind_param("s", $firstname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['npc_firstname_ID'];
    }

    // Einfügen, wenn der Vorname nicht existiert
    $stmt = $conn->prepare("INSERT INTO npc_firstname (firstname) VALUES (?)");
    $stmt->bind_param("s", $firstname);
    if ($stmt->execute()) {
        return $stmt->insert_id;
    } else {
        throw new Exception("Fehler beim Einfügen des Vornamens.");
    }
}

// Funktion, um Nachname zu speichern oder ID zu holen
function getOrInsertLastname($conn, $lastname) {
    $stmt = $conn->prepare("SELECT npc_lastname_ID FROM npc_lastname WHERE lastname = ?");
    $stmt->bind_param("s", $lastname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['npc_lastname_ID'];
    }

    // Einfügen, wenn der Nachname nicht existiert
    $stmt = $conn->prepare("INSERT INTO npc_lastname (lastname) VALUES (?)");
    $stmt->bind_param("s", $lastname);
    if ($stmt->execute()) {
        return $stmt->insert_id;
    } else {
        throw new Exception("Fehler beim Einfügen des Nachnamens.");
    }
}

// Funktion, um vollständigen Namen zu speichern oder ID zu holen
function getOrInsertFullname($conn, $firstnameID, $lastnameID) {
  if (!$firstnameID) {
      throw new Exception("Vorname-ID fehlt.");
  }

  // Überprüfen, ob bereits ein Eintrag existiert
  $stmt = $conn->prepare("SELECT npc_fullname_ID FROM npc_fullname WHERE npc_firstname_ID = ? AND npc_lastname_ID <=> ?");
  $stmt->bind_param("ii", $firstnameID, $lastnameID); // `<=>` berücksichtigt NULL-Werte
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
      return $result->fetch_assoc()['npc_fullname_ID'];
  }

  // Eintrag erstellen
  $stmt = $conn->prepare("INSERT INTO npc_fullname (npc_firstname_ID, npc_lastname_ID) VALUES (?, ?)");
  $stmt->bind_param("ii", $firstnameID, $lastnameID);
  if ($stmt->execute()) {
      return $stmt->insert_id;
  } else {
      throw new Exception("Fehler beim Einfügen in npc_fullname: " . $stmt->error);
  }
}


?>
