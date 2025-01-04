<?php
$host = '127.0.0.1';
$user = 'root';
$password = ''; // Hier das korrekte Passwort einfügen
$database = 'dnd';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
} else {
    echo "Verbindung erfolgreich!";
}
?>
