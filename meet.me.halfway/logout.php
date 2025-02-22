<?php
session_start();
session_destroy(); // Supprime toutes les sessions
echo "<script>alert('Déconnexion réussie.'); window.location.href='index.php';</script>";
exit();
?>
