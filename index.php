<?php
session_start(); // Démarre la session

// Vérifie si l'utilisateur est connecté
if (isset($_SESSION['user'])) {
    // Redirige vers une autre page si l'utilisateur est déjà connecté
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css.css">
    <title>Authentification</title>
</head>
<body class="login">
<form class="login-form" action="PHP/login.php" method="post">
    <h2>Authentification</h2>

    <!-- Section pour afficher les erreurs -->
    <?php
    if (isset($_GET['error']) && !empty($_GET['error'])): ?>
        <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <label for="username">Nom d'utilisateur</label>
    <input type="text" id="username" name="username" placeholder="Entrez votre nom d'utilisateur" required>

    <label for="password">Mot de passe</label>
    <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>

    <input type="submit" value="Se connecter">
</form>
</body>
</html>
