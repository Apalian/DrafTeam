<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Gestion des Joueurs</title>
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
    <h1>Gestion des Joueurs</h1>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="error-message">
            <p>Erreur lors de la suppression du joueur, ce joueur a participé à des matchs !</p>
        </div>
    <?php endif; ?>

    <a href="../Controller/AjouterJoueurController.php"><button>Créer un Nouveau Joueur</button></a>

    <div class="joueurs-list">
        <?php if (!empty($joueurs)): ?>
            <?php foreach ($joueurs as $joueur): ?>
                <div class="card">
                    <div class="card-body">
                        <div class="card-left">
                            <h2><?php echo htmlspecialchars($joueur->getNom()) . ' ' . htmlspecialchars($joueur->getPrenom()); ?></h2>
                            <p><strong>Numéro de Licence:</strong> <?php echo htmlspecialchars($joueur->getNumLicense()); ?></p>
                            <p><strong>Date de naissance:</strong> <?php echo htmlspecialchars($joueur->getDateNaissance()); ?></p>
                            <p><strong>Statut:</strong> <?php echo htmlspecialchars($joueur->getStatut()); ?></p>
                            <p><strong>Commentaire:</strong> <?php echo htmlspecialchars($joueur->getCommentaire()); ?></p>
                        </div>

                        <div class="card-right">
                            <p><strong>Taille:</strong> <?php echo htmlspecialchars($joueur->getTaille()); ?>cm</p>
                            <p><strong>Poids:</strong> <?php echo htmlspecialchars($joueur->getPoids()); ?>kg</p>
                        </div>
                    </div>
                    <div class="card-buttons">
                        <a href="../Controller/ModifierJoueurController.php?numLicense=<?php echo $joueur->getNumLicense(); ?>"><button>Modifier</button></a>
                        <a href="?delete=<?php echo $joueur->getNumLicense(); ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce joueur ?');"><button>Supprimer</button></a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun joueur trouvé.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
