<?php
require 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['chauffeur_id'], $_POST['destination'], $_POST['date_heure'])) {
        die("Données manquantes !");
    }

    $chauffeur_id = intval($_POST['chauffeur_id']);
    $destination = trim($_POST['destination']);
    $date_heure = $_POST['date_heure'];
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        die("Erreur : Vous devez être connecté pour commander.");
    }

    try {
        $stmt = $conn->prepare("INSERT INTO commandes (user_id, chauffeur_id, destination, date_heure) VALUES (:user_id, :chauffeur_id, :destination, :date_heure)");
        $stmt->execute([
            ':user_id' => $user_id,
            ':chauffeur_id' => $chauffeur_id,
            ':destination' => $destination,
            ':date_heure' => $date_heure
        ]);

        echo "Commande enregistrée avec succès.";
    } catch (PDOException $e) {
        die("Erreur SQL : " . $e->getMessage());
    }
}
?>
