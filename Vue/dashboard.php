<?php

// Affichage des erreurs sur Hostinger
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    <script>
        // Fonction pour charger automatiquement les statistiques d'un joueur
        function loadPlayerStats(numLicense) {
            if (!numLicense) {
                document.getElementById('stats-container').innerHTML = ''; // Vider les stats si aucun joueur sélectionné
                return;
            }

            fetch(`dashboard.php?numLicense=${encodeURIComponent(numLicense)}`)
                .then(response => response.text())
                .then(html => {
                    console.log(`dashboard.php?numLicense=${encodeURIComponent(numLicense)}`)
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const stats = doc.querySelector('#stats-container').innerHTML;
                    document.getElementById('stats-container').innerHTML = stats;
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des statistiques :', error);
                    document.getElementById('stats-container').innerHTML = '<p>Erreur lors du chargement des statistiques.</p>';
                });
        }
    </script>
</head>
<body>
<nav class="navbar">
    <div class="navbar-logo"><a href="../index.php" class="nav-link">DrafTeam</a></div>
    <div class="navbar-links">
        <a href="./gestionJoueurs.php" class="nav-link">Joueurs</a>
        <a href="./gestionMatchs.php" class="nav-link">Matchs</a>
        <a href="./dashboard.php" class="nav-link">Statistiques</a>
        <a href="../Controller/logout.php" class="nav-link logout">Déconnexion</a>
    </div>
</nav>
<div class="container">
    <h1 class="form-title">Sélectionner un Joueur</h1>
    <form class="selection-form">
        <div class="form-group">
            <label for="numLicense">Choisissez un joueur :</label>
            <select id="numLicense" name="numLicense" class="form-input" onchange="loadPlayerStats(this.value)">
                <option value="">-- Sélectionnez un joueur --</option>
                <?php foreach ($joueurs as $joueur): ?>
                    <option value="<?= htmlspecialchars($joueur->getNumLicense()) ?>">
                        <?= htmlspecialchars($joueur->getNom() . ' ' . $joueur->getPrenom()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <!-- Conteneur des statistiques -->
    <div id="stats-container">
        <?php if (isset($_GET['numLicense']) && !empty($_GET['numLicense'])): ?>
            <h2>Statistiques du joueur</h2>
            <table class="stats-table">
                <thead>
                <tr>
                    <th>Statistique</th>
                    <th>Valeur</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Poste préféré</td>
                    <td><?= htmlspecialchars($postePref ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <td>Nombre total de sélections en tant que titulaire</td>
                    <td><?= htmlspecialchars($totTitu ?? '0') ?></td>
                </tr>
                <tr>
                    <td>Nombre total de sélections en tant que remplaçant</td>
                    <td><?= htmlspecialchars($totRemp ?? '0') ?></td>
                </tr>
                <tr>
                    <td>Pourcentage de matchs gagnés</td>
                    <td><?= htmlspecialchars(number_format($pourMatchG ?? 0, 2)) ?>%</td>
                </tr>
                <tr>
                    <td>Moyenne des évaluations</td>
                    <td><?= htmlspecialchars(number_format($moyEvaluation ?? 0, 2)) ?></td>
                </tr>
                <tr>
                    <td>Nombre de sélections consécutives</td>
                    <td><?= htmlspecialchars($selectionConsecutive ?? '0') ?></td>
                </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
