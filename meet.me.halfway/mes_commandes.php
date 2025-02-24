<?php
require 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour voir vos commandes.");
}

$user_id = $_SESSION['user_id'];

try {
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Récupérer les commandes de l'utilisateur
    $stmt = $conn->prepare("
        SELECT c.id, c.destination, c.date_heure, u.name AS chauffeur_name, u.vehicule
        FROM commandes c
        JOIN users u ON c.chauffeur_id = u.id
        WHERE user_id = ?
        ORDER BY c.date_heure DESC
    ");
    $stmt->execute([$user_id]);
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Commandes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="aboutus.php">À propos</a></li>
                <li><a href="mes_commandes.php">Mes Commandes</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php">Déconnexion</a></li>
                    <li class="welcome-text">Bienvenue, <?= htmlspecialchars($_SESSION['user_name']); ?></li>
                <?php else: ?>
                    <li><a href="login.php">Connexion</a></li>
                    <li><a href="register.php">Créer un compte</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <h1>Mes Commandes</h1>

    <?php if (!empty($commandes)) : ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Chauffeur</th>
                <th>Véhicule</th>
                <th>Destination</th>
                <th>Date & Heure</th>
            </tr>
            <?php foreach ($commandes as $commande) : ?>
                <tr>
                    <td><?= htmlspecialchars($commande['id']) ?></td>
                    <td><?= htmlspecialchars($commande['chauffeur_name']) ?></td>
                    <td><?= htmlspecialchars($commande['vehicule']) ?></td>
                    <td><?= htmlspecialchars($commande['destination']) ?></td>
                    <td><?= htmlspecialchars($commande['date_heure']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else : ?>
        <p>Aucune commande passée.</p>
    <?php endif; ?>

    <a href="index.php">Retour</a>
</body>
</html>
