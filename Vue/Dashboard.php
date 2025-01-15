<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>DrafTeam</title>
    <script>
        function loadPlayerStats(numLicense) {
            if (!numLicense) {
                console.log("Aucun joueur sélectionné, conteneur vidé.");
                document.getElementById('stats-container').innerHTML = '';
                return;
            }

            console.log(`Chargement des statistiques pour le joueur avec numLicense : ${numLicense}`);

            fetch(`../Controller/DashboardController.php?numLicense=${encodeURIComponent(numLicense)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Erreur réseau : ${response.status} - ${response.statusText}`);
                    }
                    return response.text();
                })
                .then(html => {
                    console.log("HTML reçu :", html); // Affiche tout le HTML retourné
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // Vérification de l'existence du conteneur dans la réponse
                    const stats = doc.querySelector('#stats-container');
                    if (stats) {
                        console.log("Contenu extrait :", stats.innerHTML); // Logue le contenu extrait
                        document.getElementById('stats-container').innerHTML = stats.innerHTML;
                    } else {
                        console.error("Le conteneur #stats-container est introuvable dans la réponse.");
                        document.getElementById('stats-container').innerHTML = '<p>Aucune statistique trouvée.</p>';
                    }
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
        <a href="../Controller/GestionJoueursController.php" class="nav-link">Joueurs</a>
        <a href="../Controller/GestionMatchsController.php" class="nav-link">Matchs</a>
        <a href="../Controller/LogoutController.php" class="nav-link logout">Déconnexion</a>
    </div>
</nav>
<div class="container">
    <h1 class="form-title">Statistiques des Joueurs</h1>
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
        <?php if (!empty($_GET['numLicense'])): ?>
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
                    <td><?= htmlspecialchars($stats['postePref'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <td>Nombre total de sélections en tant que titulaire</td>
                    <td><?= htmlspecialchars($stats['totTitu'] ?? '0') ?></td>
                </tr>
                <tr>
                    <td>Nombre total de sélections en tant que remplaçant</td>
                    <td><?= htmlspecialchars($stats['totRemp'] ?? '0') ?></td>
                </tr>
                <tr>
                    <td>Pourcentage de matchs gagnés</td>
                    <td><?= htmlspecialchars(number_format($stats['pourMatchG'] ?? 0, 2)) ?>%</td>
                </tr>
                <tr>
                    <td>Moyenne des évaluations de la vitesse</td>
                    <td><?= htmlspecialchars(number_format($stats['moyVitesse'] ?? 0, 2)) ?></td>
                </tr>
                <tr>
                    <td>Moyenne des évaluations de endurance</td>
                    <td><?= htmlspecialchars(number_format($stats['moyEndurance'] ?? 0, 2)) ?></td>
                </tr>
                <tr>
                    <td>Moyenne des évaluations de la défense</td>
                    <td><?= htmlspecialchars(number_format($stats['moyDefense'] ?? 0, 2)) ?></td>
                </tr>
                <tr>
                    <td>Moyenne des évaluations des tirs</td>
                    <td><?= htmlspecialchars(number_format($stats['moyTirs'] ?? 0, 2)) ?></td>
                </tr>
                <tr>
                    <td>Moyenne des évaluations des passes</td>
                    <td><?= htmlspecialchars(number_format($stats['moyPasses'] ?? 0, 2)) ?></td>
                </tr>
                <tr>
                    <td>Nombre de sélections consécutives</td>
                    <td><?= htmlspecialchars($stats['selectionConsecutive'] ?? '0') ?></td>
                </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
