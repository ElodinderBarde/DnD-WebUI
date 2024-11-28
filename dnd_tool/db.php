<?php
// Datenbank-Verbindungsparameter
$host = '127.0.0.1';        
$user = 'root';             
$password = '';             // Passwort leer lassen, wenn nicht gesetzt
$database = 'dnd';          

// Verbindung herstellen
$conn = new mysqli($host, $user, $password, $database);

// Überprüfe die Verbindung
if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}

// UTF-8-Encoding setzen
$conn->set_charset("utf8");
?>
