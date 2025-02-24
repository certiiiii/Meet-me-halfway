<?php
$servername = "109.234.161.108";
$username = "xtjh1161_camille_cloupet";
$password = "3T2Em,(dW.W1";
$dbname = "xtjh1161_camille_cloupet_bdd";

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
