<?php
require 'config.php';
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Chauffeur invalide !");
}

$chauffeur_id = intval($_GET['id']);

try {
    // Connexion à la base de données
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Récupérer les infos du chauffeur
    $stmt = $conn->prepare("SELECT name, vehicule, ville FROM users WHERE id = :chauffeur_id AND role = 'chauffeur'");
    $stmt->execute([':chauffeur_id' => $chauffeur_id]);
    $chauffeur = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$chauffeur) {
        die("Chauffeur introuvable !");
    }

    // Récupérer les avis du chauffeur
    $stmtAvis = $conn->prepare("SELECT a.note, a.commentaire, u.name AS utilisateur, a.date_avis 
                                FROM avis a 
                                JOIN users u ON a.utilisateur_id = u.id 
                                WHERE a.chauffeur_id = :chauffeur_id 
                                ORDER BY a.date_avis DESC");
    $stmtAvis->execute([':chauffeur_id' => $chauffeur_id]);
    $avis = $stmtAvis->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil du Chauffeur</title>
    <link rel="stylesheet" href="styles.css">
</head>

<header>
    <nav class="navbar">
        <ul>
            <li><a href="index.php">Accueil</a></li>
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
                <strong><?= htmlspecialchars($a['utilisateur']) ?></strong> - <span class="note"><?= $a['note'] ?>/5</span> <br>
                <em><?= htmlspecialchars($a['commentaire']) ?></em> <br>
                <small>Posté le <?= htmlspecialchars($a['date_avis']) ?></small>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else : ?>
    <p>Aucun avis pour ce chauffeur.</p>
<?php endif; ?>


</body>
</html>
