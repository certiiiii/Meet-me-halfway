<?php
// Informations de connexion à la base de données
$servername = "localhost"; // Adresse du serveur MySQL (ex: localhost ou une IP)
$username = "root";        // Nom d'utilisateur MySQL
$password = "";            // Mot de passe MySQL (laisser vide si non défini)
$dbname = "nom_de_la_base"; // Nom de la base de données à utiliser

// Définition du DSN pour PDO
$dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8";

try {
    // Connexion à la base de données avec PDO
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Activer les erreurs PDO
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Récupération des résultats sous forme de tableau associatif
    ]);
} catch (PDOException $e) {
    // En cas d'erreur, afficher un message et stopper l'exécution
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
