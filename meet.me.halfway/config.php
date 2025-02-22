<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "taxi_service";

// Définition du DSN pour PDO
$dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8";

try {
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
