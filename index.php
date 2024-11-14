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
    <link rel="stylesheet" type="text/css" href="Vue/css.css">
    <title>Page d'accueil - DrafTeam</title>
</head>
<body>
<div class="container">
    <h1>Bienvenue sur la page d'accueil de DrafTeam!</h1>
    <a href="Controller/logout.php">Déconnexion</a>
</div>
</body>
</html>
