<?php
require 'config.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['chauffeur_id'], $_POST['note'], $_POST['commentaire'])) {
        die("Données manquantes !");
    }

    $chauffeur_id = intval($_POST['chauffeur_id']);
    $note = intval($_POST['note']);
    $commentaire = trim($_POST['commentaire']);
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        die("Erreur : Vous devez être connecté pour noter un chauffeur.");
    }

    if ($note < 1 || $note > 5) {
        die("Erreur : La note doit être entre 1 et 5.");
    }

    if (empty($commentaire)) {
        die("Erreur : Le commentaire ne peut pas être vide.");
    }

    try {
        $stmt = $conn->prepare("INSERT INTO avis (utilisateur_id, chauffeur_id, note, commentaire, date_avis) 
                                VALUES (:user_id, :chauffeur_id, :note, :commentaire, NOW())");
        $stmt->execute([
            ':user_id' => $user_id,
            ':chauffeur_id' => $chauffeur_id,
            ':note' => $note,
            ':commentaire' => $commentaire
        ]);

        echo "success";
    } catch (PDOException $e) {
        die("Erreur SQL : " . $e->getMessage());
    }
}
?>
