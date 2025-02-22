<?php
require 'config.php'; // Connexion à la base de données
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (!in_array($role, ['utilisateur', 'chauffeur'])) {
        echo "<script>alert('Rôle invalide.'); window.history.back();</script>";
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Récupérer les champs spécifiques au chauffeur
    $vehicule = ($role === "chauffeur") ? trim($_POST['vehicule']) : null;
    $ville = ($role === "chauffeur") ? trim($_POST['ville']) : null;
    $experience = ($role === "chauffeur") ? trim($_POST['experience']) : null;

    try {
        $conn->beginTransaction();

        // Insérer l'utilisateur avec toutes les données
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, vehicule, ville, experience) 
                                VALUES (:name, :email, :password, :role, :vehicule, :ville, :experience)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':vehicule', $vehicule);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':experience', $experience);
        $stmt->execute();

        $conn->commit();

        echo "<script>alert('Compte créé avec succès ! Vous allez être redirigé vers la page de connexion.'); window.location.href='login.php';</script>";
        exit();
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "<script>alert('Erreur : " . $e->getMessage() . "'); window.history.back();</script>";
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
</head>
<body>
    <h1>Créer un compte</h1>
    <form action="register.php" method="POST">
    <label for="name">Nom :</label>
    <input type="text" name="name" required>

    <label for="email">Email :</label>
    <input type="email" name="email" required>

    <label for="password">Mot de passe :</label>
    <input type="password" name="password" required>

    <label for="role">Type de compte :</label>
<select name="role" id="role" required onchange="toggleChauffeurFields()">
    <option value="utilisateur">Utilisateur</option>
    <option value="chauffeur">Chauffeur</option>
</select>

<div id="chauffeurFields" style="display: none;">
    <label for="vehicule">Véhicule :</label>
    <input type="text" name="vehicule">

    <label for="ville">Ville :</label>
    <input type="text" name="ville">

    <label for="experience">Expérience (années) :</label>
    <input type="number" name="experience">
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
