<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../styles.css">
    <title>Authentification - DrafTeam</title>
</head>
<body>
<div class="login">
    <form class="login-form" action="../Controller/LoginController.php" method="post">
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
