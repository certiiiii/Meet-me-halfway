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
    // Récupérer les infos du chauffeur
    $stmt = $conn->prepare("SELECT name, vehicule, ville FROM users WHERE id = ? AND role = 'chauffeur'");
    $stmt->execute([$chauffeur_id]);
    $chauffeur = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$chauffeur) {
        die("Chauffeur non trouvé.");
    }

    // Récupérer les avis
    $stmt = $conn->prepare("SELECT u.name AS utilisateur, a.note, a.commentaire, a.date_avis FROM avis a JOIN users u ON a.utilisateur_id = u.id WHERE a.chauffeur_id = ?");
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
    <link rel="icon" type="image/png" href="images/favicon.ico">
</head>

<header>
    <nav class="navbar">
        <ul>
            <img src="images/logo.png" alt="Logo meet me halfway" class="logo">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="aboutus.php">À propos</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="mes_commandes.php">Mes Commandes</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="login.php">Connexion</a></li>
                <li><a href="register.php">Créer un compte</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<body>
<h1>Profil du Chauffeur</h1>
<h2><?= htmlspecialchars($chauffeur['name']) ?></h2>
<p><strong>Véhicule :</strong> <?= htmlspecialchars($chauffeur['vehicule']) ?></p>
<p><strong>Ville :</strong> <?= htmlspecialchars($chauffeur['ville']) ?></p>

<h2>Avis des utilisateurs</h2>
<?php if (!empty($avis)) : ?>
    <ul class="avl">
        <?php foreach ($avis as $a) : ?>
            <li>
                <strong><?= htmlspecialchars($a['utilisateur']) ?></strong><br>
                <em><?= htmlspecialchars($a['commentaire']) ?></em> - <span class="note"><?= $a['note'] ?>/5</span> <br>
                <small>Posté le <?= htmlspecialchars($a['date_avis']) ?></small>
            </li>
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
