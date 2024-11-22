
<?php
session_start();

// Vérification de la session
if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
    header("Location: ../Vue/login.php");
    exit();
}

require_once '../Modele/Database.php';
require_once '../Modele/Dao/DaoJoueurs.php';

// Initialisation du DAO
$daoJoueurs = new \Modele\Dao\DaoJoueurs($_SESSION['username'], $_SESSION['password']);

// Récupération de tous les joueurs
try {
    $joueurs = $daoJoueurs->findAll();
} catch (Exception $e) {
    die('Erreur lors du chargement des joueurs : ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>DrafTeam</title>
</head>
<body>
<nav class="navbar">
    <div class="navbar-logo"><a href="./index.php" class="nav-link">DrafTeam</a></div>
    <div class="navbar-links">
        <a href="./Vue/gestionJoueurs.php" class="nav-link">Joueurs</a>
        <a href="./Vue/gestionMatchs.php" class="nav-link">Matchs</a>
        <a href="./Vue/dashboard.php" class="nav-link">Statistiques</a>
        <a href="./Controller/logout.php" class="nav-link logout">Déconnexion</a>
    </div>

</nav>
    <div class="container">
        <h1 class="form-title">Sélectionner un Joueur</h1>
        <form method="GET" action="afficherStatsJoueur.php" class="selection-form">
            <div class="form-group">
                <label for="numLicense">Choisissez un joueur :</label>
                <select id="numLicense" name="numLicense" class="form-input" required>
                    <option value="">-- Sélectionnez un joueur --</option>
                    <?php foreach ($joueurs as $joueur): ?>
                        <option value="<?= htmlspecialchars($joueur->getNumLicense()) ?>">
                            <?= htmlspecialchars($joueur->getNom() . ' ' . $joueur->getPrenom()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-buttons">
                <button type="submit" class="btn-submit">Afficher les Statistiques</button>
                <a href="dashboard.php" class="btn-cancel">Annuler</a>
            </div>
        </form>
    </div>
    </body>
    </html>
