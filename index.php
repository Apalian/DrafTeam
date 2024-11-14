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
    <title>DrafTeam</title>
</head>
<body>
<!-- Barre de navigation -->
<nav class="navbar">
    <div class="navbar-logo"><a href="./index.php" class="nav-link">DrafTeam</a></div>
    <div class="navbar-links">
        <a href="./Vue/gestionJoueurs.php" class="nav-link">Joueurs</a>
        <a href="#" class="nav-link">Matchs</a>
        <a href="#" class="nav-link">Statistiques</a>
        <a href="#" class="nav-link">Équipes</a>
        <a href="./Controller/logout.php" class="nav-link logout">Déconnexion</a>
    </div>
</nav>

<!-- Contenu de la page -->
<div class="content">
    <h1>Bienvenue sur DrafTeam!</h1>
    <p>Votre plateforme de gestion de matchs de Handball.</p>
</div>
</body>
</html>

