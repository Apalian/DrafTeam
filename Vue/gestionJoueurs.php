<?php

$_SESSION = session_start();
if (isset($_SESSION['username']) && isset($_SESSION['password'])){
    header("Location: index.php");
}else{

    require_once './PHP/Database.php';
    require_once './PHP/Modele/Dao/DaoJoueurs.php';

    $daoJoueurs = new \Modele\Dao\DaoJoueurs($_SESSION['username'], $_SESSION['password']);
    $joueurs = $daoJoueurs->findAll();

    // Vérifier si un joueur a été supprimé
    if (isset($_GET['delete'])) {
        $numLicense = $_GET['delete'];
        $daoJoueurs->delete($numLicense);
        header("Location: gestion_joueurs.php");
        exit;
    }

    ?>

    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css.css">
        <title>Gestion des Joueurs</title>
    </head>
    <body>

    <div class="container">
        <h1>Gestion des Joueurs</h1>
        <a href="ajouter_joueur.php"><button>Créer un Nouveau Joueur</button></a>

        <div class="joueurs-list">
            <?php foreach ($joueurs as $joueur): ?>
                <div class="card">
                    <h2><?php echo htmlspecialchars($joueur['nom']) . ' ' . htmlspecialchars($joueur['prenom']); ?></h2>
                    <p><strong>Numéro de Licence:</strong> <?php echo htmlspecialchars($joueur['numLicense']); ?></p>
                    <p><strong>Date de naissance:</strong> <?php echo htmlspecialchars($joueur['dateNaissance']); ?></p>
                    <p><strong>Statut:</strong> <?php echo htmlspecialchars($joueur['statut']); ?></p>
                    <p><strong>Commentaire:</strong> <?php echo htmlspecialchars($joueur['commentaire']); ?></p>

                    <!-- Boutons Modifier et Supprimer -->
                    <a href="modifier_joueur.php?numLicense=<?php echo $joueur['numLicense']; ?>"><button>Modifier</button></a>
                    <a href="?delete=<?php echo $joueur['numLicense']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce joueur ?');"><button>Supprimer</button></a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    </body>
    </html>

<?php } ?>
