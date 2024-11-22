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

// Inclure DaoMatchs et récupérer les statistiques
require_once './Modele/Dao/DaoMatchs.php';
require_once './Modele/Database.php'; // Fichier contenant la configuration PDO.

$daoMatchs = new \Modele\Dao\DaoMatchs($_SESSION['username'], $_SESSION['password']);
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
    $error = "Erreur lors de la récupération des statistiques : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>DrafTeam</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Ajout de Chart.js -->
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

<!-- Contenu de la page -->
<div class="content">
    <h1>Bienvenue sur DrafTeam!</h1>
    <p>Votre plateforme de gestion de matchs de Handball.</p>

    <!-- Section pour le graphique -->
    <div>
        <h2>Statistiques des matchs</h2>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php else: ?>
            <canvas  id="pieChart"></canvas>
            <script>
                // Données des statistiques
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

                // Configuration du graphique
                const config = {
                    type: 'pie',
                    data: data,
                };

                const config = {
                    type: 'pie',
                    data: data,
                    options: {
                        maintainAspectRatio: false,
                        aspectRatio: 1.5 // Adjust this ratio to make the chart smaller or larger
                    }
                };

                // Initialisation du graphique
                const pieChart = new Chart(
                    document.getElementById('pieChart'),
                    config
                );

            </script>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
