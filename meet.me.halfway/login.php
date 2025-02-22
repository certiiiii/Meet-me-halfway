<?php
require 'config.php'; // Fichier contenant la connexion à la base de données
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    try {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                echo "<script>alert('Connexion réussie ! Vous allez être redirigé vers l\'accueil.'); window.location.href='index.php';</script>";
                exit();
            } else {
                echo "<script>alert('Mot de passe incorrect.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Utilisateur non trouvé. Veuillez vous inscrire.'); window.location.href='register.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Erreur : " . $e->getMessage() . "'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Connexion</h1>
    <form method="POST">
        <label for="email">Email :</label>
        <input type="email" name="email" required>
        <label for="password">Mot de passe :</label>
        <input type="password" name="password" required>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
