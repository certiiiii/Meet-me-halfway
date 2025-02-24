<?php
require 'config.php'; 
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($dsn) || !isset($username) || !isset($password)) {
    die("Erreur de configuration de la base de données.");
}

try {
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Récupération du rôle de l'utilisateur
$user_role = $_SESSION['user_role'] ?? 'utilisateur';

// Requête pour afficher les chauffeurs uniquement aux utilisateurs
if ($user_role === 'utilisateur') {
    $sql = "SELECT id, name, vehicule, ville, 
    (SELECT AVG(note) FROM avis WHERE avis.chauffeur_id = users.id) AS note_moyenne 
    FROM users WHERE role = 'chauffeur'";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $chauffeurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="images/favicon.ico">
    <title>meet me halfway</title>
</head>
<body>

<header>
    <nav class="navbar">
        <ul>
            <img src="images/logo.png" alt="Logo meet me halfway" class="logo">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="aboutus.php">À propos</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="mes_commandes.php">Mes Commandes</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
                <li class="welcome-text">Bienvenue, <?= htmlspecialchars($_SESSION['user_name']); ?> (<?= $user_role ?>)</li>
            <?php else: ?>
                <li><a href="login.php">Connexion</a></li>
                <li><a href="register.php">Créer un compte</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<h1>Bienvenue sur Meet me halfway</h1>

<?php if ($user_role === 'utilisateur'): ?>
    <h2>Liste des Chauffeurs</h2>
    <table>
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Véhicule</th>
        <th>Ville</th>
        <th>Note Moyenne</th>
        <th>Action</th>
    </tr>
    <?php if (!empty($chauffeurs)) {
        foreach ($chauffeurs as $row) {
            $note = $row['note_moyenne'] !== null ? number_format($row['note_moyenne'], 1) : 'Pas encore noté';
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td><a href='avis.php?chauffeur_id={$row['id']}'>{$row['name']}</a></td>
                    <td>{$row['vehicule']}</td>
                    <td>{$row['ville']}</td>
                    <td>{$note}</td>
                    <td><button class='btn commander-btn' data-chauffeur='{$row['id']}'>Commander</button></td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='6'>Aucun chauffeur trouvé</td></tr>";
    }
    ?>
</table>
<!-- MODAL DE COMMANDE -->
<div id="commandeModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeCommandeModal()">&times;</span>
        <h2>Passer une commande</h2>
        <form id="commandeForm">
            <input type="hidden" name="chauffeur_id" id="commandeChauffeurId">
            
            <label for="destination" class="popup">Destination :</label>
            <input type="text" name="destination" required><br>

            <label for="date_heure"class="popup">Date et Heure :</label>
            <input type="datetime-local" name="date_heure" required><br>

            <button type="submit" class="btn">Confirmer</button>
        </form>
    </div>
</div>

    <h2>Donner une note à un chauffeur</h2>
    <button class="btn" onclick="openModal()">Noter un chauffeur</button>

    <div id="noteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Ajouter un Avis</h2>
            <form id="noteForm">
                <label for="chauffeur_id" class="popup" >ID du Chauffeur :</label>
                <input type="number" name="chauffeur_id" required><br>
                
                <label for="note" class="popup" >Note :</label>
                <input type="number" name="note" min="1" max="5" required><br>
                
                <label for="commentaire" class="popup">Commentaire :</label>
                <textarea name="commentaire" rows="4" required></textarea><br>
                
                <button type="submit" class="btn">Envoyer</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <h2>Bienvenue Chauffeur</h2>
    <p>Vous êtes connecté en tant que chauffeur. Vous pouvez voir vos avis et gérer votre profil.</p>
<?php endif; ?>
<script>
    function openModal() {
        document.getElementById("noteModal").style.display = "flex";
    }

    function closeModal() {
        document.getElementById("noteModal").style.display = "none";
    }

    document.getElementById("noteForm").addEventListener("submit", function(event) {
        event.preventDefault();
        
        let formData = new FormData(this);

        fetch("avis.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert("Avis ajouté avec succès !");
            closeModal();
            location.reload();
        })
        .catch(error => console.error("Erreur :", error));
    });
</script>
<script>
    document.querySelectorAll('.commander-btn').forEach(button => {
        button.addEventListener('click', function() {
            let chauffeurId = this.getAttribute('data-chauffeur');
            document.getElementById('commandeChauffeurId').value = chauffeurId;
            document.getElementById('commandeModal').style.display = 'flex';
        });
    });

    function closeCommandeModal() {
        document.getElementById('commandeModal').style.display = 'none';
    }

    document.getElementById("commandeForm").addEventListener("submit", function(event) {
        event.preventDefault();
        
        let formData = new FormData(this);

        fetch("commande.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert("Commande passée avec succès !");
            closeCommandeModal();
        })
        .catch(error => console.error("Erreur :", error));
    });
</script>

</body>
</html>

<?php
$conn = null;
?>
