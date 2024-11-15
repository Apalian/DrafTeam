<?php

session_start();

// Vérifier si l'utilisateur est connecté
if ((!isset($_SESSION['username']) || !isset($_SESSION['password'])) && !isset($_GET['numLicense'])) {
    header("Location: ../Vue/login.php");
    exit();
}
$daoJoueurs = new \Modele\Dao\DaoJoueurs($_SESSION['username'], $_SESSION['password']);
$numLicense = $_GET['numLicense'];
$joueur = $daoJoueurs ->findById($numLicense);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Gestion des Joueurs</title>
</head>
<body>

<div class="container">
    <h1>Gestion des Joueurs</h1>
    <a href="ajouterJoueur.php"><button>Créer un Nouveau Joueur</button></a>

    <div class="joueurs-list">
        <div class="card">
            <div class="card-body">
                <div class="card-left">
                    <h2><?php echo htmlspecialchars($joueur['nom']) . ' ' . htmlspecialchars($joueur['prenom']); ?></h2>
                    <p><strong>Numéro de Licence:</strong> <?php echo htmlspecialchars($joueur['numLicense']); ?></p>
                    <p><strong>Date de naissance:</strong> <?php echo htmlspecialchars($joueur['dateNaissance']); ?></p>
                    <p><strong>Statut:</strong> <?php echo htmlspecialchars($joueur['statut']); ?></p>
                    <p><strong>Commentaire:</strong> <?php echo htmlspecialchars($joueur['commentaire']); ?></p>
                </div>

                <div class="card-right">
                    <p><strong>Taille:</strong> <?php echo htmlspecialchars($joueur['taille']); ?></p>
                    <p><strong>Poids:</strong> <?php echo htmlspecialchars($joueur['poids']); ?></p>
                </div>
            </div>
            <!-- Boutons Modifier et Supprimer -->
            <div class="card-buttons">
                <a href="modifierJoueurs.php?numLicense=<?php echo $joueur['numLicense']; ?>"><button>Modifier</button></a>
                <a href="?delete=<?php echo $joueur['numLicense']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce joueur ?');"><button>Supprimer</button></a>
            </div>
        </div>
    </div>
</div>

</body>
</html>

