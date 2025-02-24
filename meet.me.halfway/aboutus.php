<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="images/favicon.ico">
    <title>meet me halfway</title>
</head>
<body>
    <header>
        <nav class="navbar">

        <nav class="navbar">
            <ul>
                <img src="images/logo.png" alt="Logo meet me halfway" class="logo">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="aboutus.php">À propos</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php">Déconnexion</a></li>
                    <li class="welcome-text">Bienvenue, <?= htmlspecialchars($_SESSION['user_name']); ?> (<?= $user_role ?>)</li>
                <?php else: ?>
                    <li><a href="login.php">Connexion</a></li>
                    <li><a href="register.php">Créer un compte</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <h1>Meet me Halfway</h1>
    <h1>À Propos</h1>
    <p>Meet Me Halfway est une plateforme innovante qui vous permet de commander un chauffeur en quelques clics. Que vous ayez un rendez-vous, un vol à prendre ou simplement besoin d'un trajet rapide et sécurisé, notre service vous offre une solution fiable pour vous déplacer efficacement d'un point A à un point B.</p>
</body>
</html>
