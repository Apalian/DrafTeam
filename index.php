<?php
// Affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
    header("Location: ./Vue/login.php");
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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>DrafTeam</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <div>
        <h2>Sélectionner un Joueur</h2>
        <?php if (!empty($errorJoueurs)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($errorJoueurs); ?></p>
        <?php else: ?>
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
            <div id="stats-container"></div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
