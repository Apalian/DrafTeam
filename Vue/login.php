<?php
// Affichage des erreurs sur Hostinger
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Vérifie si l'utilisateur est déjà connecté
if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Vérifiez les informations d'identification avec la base de données (exemple simple)
    $user = "admin";
    $pass = "admin";

    if ($username === $user && $password === $pass) {
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
        header("Location: ../index.php");
        exit();
    } else {
        header("Location: login.php?error=Nom d'utilisateur ou mot de passe incorrect");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles.css">
    <title>Authentification - DrafTeam</title>
</head>
<body>
<div class="container">
    <form class="login-form" action="login.php" method="post">
        <h2>Authentification</h2>

        <!-- Affichage des erreurs -->
        <?php if (isset($_GET['error']) && !empty($_GET['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <label for="username">Nom d'utilisateur</label>
        <input type="text" id="username" name="username" placeholder="Entrez votre nom d'utilisateur" required>

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>

        <input type="submit" value="Se connecter">
    </form>
</div>
</body>
</html>
