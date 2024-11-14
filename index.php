<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
    header("Location: ./Vue/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Navbar avec menu latéral</title>
</head>
<body>
<!-- Bouton hamburger pour ouvrir le menu -->
<div class="hamburger" onclick="toggleMenu()">☰</div>

<!-- Menu latéral (navbar) -->
<div id="sideMenu" class="side-menu">
    <div class="menu-content">
        <a href="#" class="menu-item">Accueil</a>
        <a href="#" class="menu-item">Profil</a>
        <a href="#" class="menu-item">Matchs</a>
        <a href="#" class="menu-item">Statistiques</a>
        <a href="#" class="menu-item">Équipes</a>
        <!-- Bouton de déconnexion -->
        <a href="Controller/logout.php" class="menu-item logout">Déconnexion</a>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
