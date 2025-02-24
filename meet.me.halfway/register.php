<?php
require 'config.php'; 
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (!in_array($role, ['utilisateur', 'chauffeur'])) {
        die("Rôle invalide.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Vérifier si l'utilisateur existe déjà
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        die("Cet email est déjà utilisé !");
    }

    // Champs spécifiques aux chauffeurs
    $vehicule = ($role === "chauffeur") ? trim($_POST['vehicule']) : null;
    $ville = ($role === "chauffeur") ? trim($_POST['ville']) : null;
    $experience = ($role === "chauffeur") ? (int) $_POST['experience'] : null;

    try {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, vehicule, ville, experience) 
                                VALUES (:name, :email, :password, :role, :vehicule, :ville, :experience)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role,
            ':vehicule' => $vehicule,
            ':ville' => $ville,
            ':experience' => $experience
        ]);

        echo "<script>alert('Compte créé avec succès !'); window.location.href='login.php';</script>";
        exit();
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="images/favicon.ico">
    <title>meet me halfway</title>
</head>
<body>
    <h1>Créer un compte</h1>
    <form action="register.php" method="POST">
    <label for="name">Nom :</label>
    <input type="text" name="name" required autocomplete="name">

    <label for="email">Email :</label>
    <input type="email" name="email" required autocomplete="email">

    <label for="password">Mot de passe :</label>
    <input type="password" name="password" required autocomplete="new-password">

    <label for="role">Type de compte :</label>
    <select name="role" id="role" required onchange="toggleChauffeurFields()">
        <option value="utilisateur">Utilisateur</option>
        <option value="chauffeur">Chauffeur</option>
    </select>

    <div id="chauffeurFields" style="display: none;">
        <label for="vehicule">Véhicule :</label>
        <input type="text" name="vehicule" autocomplete="off">

        <label for="ville">Ville :</label>
        <input type="text" name="ville" autocomplete="address-level2">

        <label for="experience">Expérience (années) :</label>
        <input type="number" name="experience" autocomplete="off">
    </div>

    <button type="submit">S'inscrire</button>
</form>


<script>
function toggleChauffeurFields() {
    let role = document.getElementById("role").value;
    document.getElementById("chauffeurFields").style.display = (role === "chauffeur") ? "block" : "none";
}
</script>

</body>
</html>
