<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>DrafTeam</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
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
        <?php
        // Initialisation des données avec des valeurs par défaut
        $dataGagnes = isset($stats['matchsGagnes']) ? intval($stats['matchsGagnes']) : 0;
        $dataPerdus = isset($stats['matchsPerdus']) ? intval($stats['matchsPerdus']) : 0;
        $dataNuls = isset($stats['matchsNuls']) ? intval($stats['matchsNuls']) : 0;
        ?>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
        <?php else: ?>
            <div style="width: 300px; height: 300px; margin: 0 auto;">
                <canvas id="pieChart"></canvas>
            </div>
            <script>
                // Register the ChartDataLabels plugin
                Chart.register(ChartDataLabels);

                // Data for the chart
                const data = {
                    labels: ['Gagnés', 'Perdus', 'Nuls'],
                    datasets: [{
                        data: [
                            <?php echo $dataGagnes; ?>,
                            <?php echo $dataPerdus; ?>,
                            <?php echo $dataNuls; ?>
                        ],
                        backgroundColor: ['#4CAF50', '#F44336', '#FFC107'],
                        hoverOffset: 4
                    }]
                };

                // Chart configuration
                const config = {
                    type: 'pie',
                    data: data,
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            datalabels: {
                                anchor: 'end',
                                align: 'end',
                                formatter: (value, ctx) => {
                                    const totalSum = ctx.chart.data.datasets[ctx.datasetIndex].data.reduce((accumulator, currentValue) => {
                                        return accumulator + currentValue;
                                    }, 0);
                                    if (totalSum === 0) {
                                        return "0%";
                                    }
                                    const percentage = (value / totalSum) * 100;
                                    return `${percentage.toFixed(1)}%`;
                                },
                                color: '#000',
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                };

                // Initialize the chart
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
