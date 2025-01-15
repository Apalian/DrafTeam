<?php
// Affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
    header("Location: ./Vue/Login.php");
    exit();
}

// Inclure les DAO
require_once './Modele/Dao/DaoMatchs.php';
require_once './Modele/Dao/DaoJoueurs.php';
require_once './Modele/Database.php';

// Initialiser les DAO
$daoMatchs = new \Modele\Dao\DaoMatchs($_SESSION['username'], $_SESSION['password']);
$daoJoueurs = new \Modele\Dao\DaoJoueurs($_SESSION['username'], $_SESSION['password']);

// Récupération des statistiques des matchs
$stats = [
    'totalMatchs' => 0,
    'matchsGagnes' => 0,
    'matchsPerdus' => 0,
    'matchsNuls' => 0,
    'pourcentageGagnes' => 0,
    'pourcentagePerdus' => 0,
    'pourcentageNuls' => 0,
];
try {
    $stats = $daoMatchs->getMatchStats();
} catch (Exception $e) {
    $errorMatch = "Erreur lors de la récupération des statistiques : " . $e->getMessage();
}

// Récupération des joueurs
try {
    $joueurs = $daoJoueurs->findAll();
} catch (Exception $e) {
    $errorJoueurs = "Erreur lors de la récupération des joueurs : " . $e->getMessage();
}
if (!empty($_GET['numLicense'])) {
    $numLicense = htmlspecialchars($_GET['numLicense']);
    try {
        $postePref = $daoJoueurs->getPostePrefere($numLicense);
        $totTitu = $daoJoueurs->getTotalTitulaire($numLicense);
        $totRemp = $daoJoueurs->getTotalRemplacant($numLicense);
        $pourMatchG = $daoJoueurs->getPourcentageMatchsGagnes($numLicense);
        $moyEndurance = $daoJoueurs->getMoyenneEndurance($numLicense);
        $moyVitesse = $daoJoueurs->getMoyenneVitesse($numLicense);
        $moyDefense = $daoJoueurs->getMoyenneDefense($numLicense);
        $moyTirs = $daoJoueurs->getMoyenneTirs($numLicense);
        $moyPasses = $daoJoueurs->getMoyennePasses($numLicense);
        $selectionConsecutive = $daoJoueurs->getSelectionsConsecutives($numLicense);
    } catch (Exception $e) {
        die('Erreur lors du chargement des statistiques : ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>DrafTeam</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="./script.js"></script>
</head>
<body>
<nav class="navbar">
    <div class="navbar-logo"><a href="./index.php" class="nav-link">DrafTeam</a></div>
    <div class="navbar-links">
        <a href="./Controller/GestionJoueursController.php" class="nav-link">Joueurs</a>
        <a href="./Controller/GestionMatchsController.php" class="nav-link">Matchs</a>
        <a href="./Controller/LogoutController.php" class="nav-link logout">Déconnexion</a>
    </div>
</nav>

<div class="content">
    <h1>Bienvenue sur DrafTeam!</h1>
    <p>Votre plateforme de gestion de matchs de Handball.</p>

    <!-- Section Matchs -->
    <div>
        <h2>Statistiques des matchs</h2>
        <?php if (!empty($errorMatch)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($errorMatch); ?></p>
        <?php else: ?>
            <div style="width: 200px; height: 200px; margin: 0 auto;">
                <canvas id="pieChart"></canvas>
            </div>
            <script>
                const data = {
                    labels: ['Gagnés', 'Perdus', 'Nuls'],
                    datasets: [{
                        data: [
                            <?php echo $stats['matchsGagnes']; ?>,
                            <?php echo $stats['matchsPerdus']; ?>,
                            <?php echo $stats['matchsNuls']; ?>
                        ],
                        backgroundColor: ['#4CAF50', '#F44336', '#FFC107'],
                        hoverOffset: 4
                    }]
                };

                const config = {
                    type: 'pie',
                    data: data,
                    options: {
                        maintainAspectRatio: false,
                        aspectRatio: 1
                    }
                };

                const pieChart = new Chart(
                    document.getElementById('pieChart'),
                    config
                );
            </script>
        <?php endif; ?>
    </div>

    <!-- Section Joueurs -->
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
                        <td>Moyenne des évaluations de la vitesse</td>
                        <td><?= htmlspecialchars(number_format($moyVitesse ?? 0, 2)) ?></td>
                    </tr>
                    <tr>
                        <td>Moyenne des évaluations de endurance</td>
                        <td><?= htmlspecialchars(number_format($moyEndurance ?? 0, 2)) ?></td>
                    </tr>
                    <tr>
                        <td>Moyenne des évaluations de la défense</td>
                        <td><?= htmlspecialchars(number_format($moyDefense ?? 0, 2)) ?></td>
                    </tr>
                    <tr>
                        <td>Moyenne des évaluations des tirs</td>
                        <td><?= htmlspecialchars(number_format($moyTirs ?? 0, 2)) ?></td>
                    </tr>
                    <tr>
                        <td>Moyenne des évaluations des passes</td>
                        <td><?= htmlspecialchars(number_format($moyPasses ?? 0, 2)) ?></td>
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
</div>
</body>
</html>
