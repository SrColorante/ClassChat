<?php
$host = 'localhost';
$dbname = 'chat_db';
$username = 'root';  // Modifica secondo il tuo setup
$password = '';      // Modifica secondo il tuo setup

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connessione al database fallita: " . $e->getMessage());
}
?>