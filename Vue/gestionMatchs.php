<?php
// Affichage des erreurs sur Hostinger
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
    header("Location: ../Vue/login.php");
    exit();
}

require_once '../Modele/Database.php';
require_once '../Modele/Dao/DaoMatchs.php';

$daoMatchs = new \Modele\Dao\DaoMatchs($_SESSION['username'], $_SESSION['password']);
$matchs = $daoMatchs->findAll();

// Vérifier si un Match a été supprimé
if (isset($_GET['dateMatch'])&&isset($_GET['heure'])) {
    $dateMatch = $_GET['dateMatch'];
    $heure = $_GET['heure'];
    $daoMatchs->delete($dateMatch, $heure);
    header("Location: gestionMatchs.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Gestion des Matchs</title>

</head>
<body>

<div class="container">
    <h1>Gestion des Matchs</h1>
    <a href="ajouterMatch.php"><button>Créer un Nouveau Match</button></a>

    <div class="joueurs-list">
        <?php foreach ($matchs as $match): ?>
            <div class="card">
                <div class="card-body">
                    <div class="card-left">
                        <h2><?php echo 'Match du '.htmlspecialchars($match->getDateMatch()) . ' à ' . htmlspecialchars($match->getHeure()); ?></h2>
                        <p><strong>Nom de l'équipe adverse:</strong> <?php echo htmlspecialchars($match->getNomEquipeAdverse()); ?></p>
                        <p><strong>Lieu de rencontre:</strong> <?php echo htmlspecialchars($match->getLieuRencontre()); ?></p>
                        <p>
                            <strong>Score de l'équipe domicile:</strong>
                            <span class="<?php echo ($match->getScoreEquipeExterne() == $match->getScoreEquipeDomicile()) ? 'score-gray' : (($match->getScoreEquipeDomicile() > $match->getScoreEquipeExterne()) ? 'score-green' : 'score-red'); ?>">
                                <?php echo htmlspecialchars($match->getScoreEquipeDomicile()); ?>
                            </span>
                        </p>
                        <p>
                            <strong>Score de l'équipe adverse:</strong>
                            <span class="<?php echo ($match->getScoreEquipeExterne() == $match->getScoreEquipeDomicile()) ? 'score-gray' : (($match->getScoreEquipeDomicile() < $match->getScoreEquipeExterne()) ? 'score-green' : 'score-red'); ?>">
                                <?php echo htmlspecialchars($match->getScoreEquipeExterne()); ?>
                            </span>
                        </p>
                    </div>
                </div>
                <!-- Boutons Modifier et Supprimer -->
                <div class="card-buttons">
                    <a href="modifierMatch.php?numLicense=<?php echo $match->getDateMatch(); ?>"><button>Modifier</button></a>
                    <a href="?date=<?php echo $match->getDateMatch(); ?>&heure=<?php echo $match->getHeure(); ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce match ?');"><button>Supprimer</button></a>
                </div>
            </div>

        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
