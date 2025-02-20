<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Gestion des Matchs</title>
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
    <h1>Gestion des Matchs</h1>

    <!-- Formulaire de recherche -->
    <form method="GET" action="GestionMatchsController.php" class="search-form">
        <input type="text" name="search" placeholder="Rechercher un match..." class="search-input" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit" class="search-button">Rechercher</button>
    </form>

    <a href="../Controller/AjouterMatchController.php"><button>Créer un Nouveau Match</button></a>

    <div class="joueurs-list">
        <?php if (!empty($matchs)): ?>
            <?php foreach ($matchs as $match): ?>
                <div class="card">
                    <div class="card-body">
                        <div class="card-left">
                            <h2><?php echo 'Match du '.htmlspecialchars($match->getDateMatch()) . ' à ' . htmlspecialchars($match->getHeure()); ?></h2>
                            <p><strong>Nom de l'équipe adverse:</strong> <?php echo htmlspecialchars($match->getNomEquipeAdverse()); ?></p>
                            <p><strong>Lieu de rencontre:</strong> <?php echo htmlspecialchars($match->getLieuRencontre()); ?></p>
                            <p>
                                <strong>Score de l'équipe domicile:</strong>
                                <?php
                                $scoreDomicile = $match->getScoreEquipeDomicile();
                                $scoreExterne = $match->getScoreEquipeExterne();

                                if ($scoreDomicile === null || $scoreExterne === null) {
                                    $classDomicile = 'score-unknown';
                                } elseif ($scoreDomicile == $scoreExterne) {
                                    $classDomicile = 'score-gray';
                                } elseif ($scoreDomicile > $scoreExterne) {
                                    $classDomicile = 'score-green';
                                } else {
                                    $classDomicile = 'score-red';
                                }
                                ?>
                                <span class="<?php echo $classDomicile; ?>">
                                    <?php echo ($scoreDomicile === null) ? 'pas de score' : htmlspecialchars($scoreDomicile); ?>
                                </span>
                            </p>

                            <p>
                                <strong>Score de l'équipe externe:</strong>
                                <?php
                                if ($scoreDomicile === null || $scoreExterne === null) {
                                    $classExterne = 'score-unknown';
                                } elseif ($scoreDomicile == $scoreExterne) {
                                    $classExterne = 'score-gray';
                                } elseif ($scoreExterne > $scoreDomicile) {
                                    $classExterne = 'score-green';
                                } else {
                                    $classExterne = 'score-red';
                                }
                                ?>
                                <span class="<?php echo $classExterne; ?>">
                                    <?php echo ($scoreExterne === null) ? 'pas de score' : htmlspecialchars($scoreExterne); ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="card-buttons">
                        <a href="../Controller/ModifierMatchController.php?matchId=<?php echo $match->getId(); ?>"><button>Modifier</button></a>
                        <?php if (!$match->isMatchPassed()): ?>
                            <a href="?delete=<?php echo $match->getId(); ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce match ?');"><button>Supprimer</button></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun match trouvé.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
