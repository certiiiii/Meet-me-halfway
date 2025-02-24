<?php
require 'config.php';
session_start();
$conn = new PDO($dsn, $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// ✅ SI REQUÊTE POST : AJOUTER UN AVIS
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['chauffeur_id'], $_POST['note'], $_POST['commentaire'])) {
        die("Données manquantes.");
    }

    $chauffeur_id = intval($_POST['chauffeur_id']);
    $note = intval($_POST['note']);
    $commentaire = trim($_POST['commentaire']);
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        die("Vous devez être connecté pour laisser un avis.");
    }
    if ($note < 1 || $note > 5) {
        die("La note doit être entre 1 et 5.");
    }
    if (empty($commentaire)) {
        die("Le commentaire ne peut pas être vide.");
    }

    try {
        // Vérifier si le chauffeur existe
        $stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'chauffeur'");
        $stmt->execute([$chauffeur_id]);
        if ($stmt->rowCount() === 0) {
            die("Le chauffeur sélectionné n'existe pas.");
        }

        // Insérer l'avis
        $stmt = $conn->prepare("INSERT INTO avis (chauffeur_id, utilisateur_id, note, commentaire) VALUES (?, ?, ?, ?)");
        $stmt->execute([$chauffeur_id, $user_id, $note, $commentaire]);

        echo "success";
    } catch (PDOException $e) {
        die("Erreur d'enregistrement : " . $e->getMessage());
    }
    exit;
}

// ✅ SI REQUÊTE GET : AFFICHER LES AVIS
if (!isset($_GET['chauffeur_id']) || empty($_GET['chauffeur_id'])) {
    die("ID du chauffeur manquant.");
}

$chauffeur_id = intval($_GET['chauffeur_id']);

try {
    // Récupérer les avis
    $stmt = $conn->prepare("SELECT utilisateur_id, note, commentaire FROM avis WHERE chauffeur_id = ?");
    $stmt->execute([$chauffeur_id]);
    $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Avis du chauffeur</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Avis sur le chauffeur</h1>
    
    <?php if (!empty($avis)) : ?>
        <ul>
            <?php foreach ($avis as $row) : ?>
                <li>Note : <?= htmlspecialchars($row['note']); ?>/5 - <?= htmlspecialchars($row['commentaire']); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>Aucun avis pour ce chauffeur.</p>
    <?php endif; ?>

    <!-- Formulaire pour ajouter un avis -->
    <h2>Donner une note</h2>
    <form id="noteForm">
        <input type="hidden" name="chauffeur_id" value="<?= htmlspecialchars($chauffeur_id) ?>">
        
        <label for="note">Note :</label>
        <input type="number" name="note" min="1" max="5" required><br>
        
        <label for="commentaire">Commentaire :</label>
        <textarea name="commentaire" rows="4" required></textarea><br>
        
        <button type="submit" class="btn">Envoyer</button>
    </form>

    <a href="index.php">Retour</a>

    <script>
        document.getElementById("noteForm").addEventListener("submit", function(event) {
            event.preventDefault();
            
            let formData = new FormData(this);

            fetch("avis.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data === "success") {
                    alert("Avis ajouté avec succès !");
                    location.reload();
                } else {
                    alert("Erreur : " + data);
                }
            })
            .catch(error => console.error("Erreur :", error));
        });
    </script>
</body>
</html>
