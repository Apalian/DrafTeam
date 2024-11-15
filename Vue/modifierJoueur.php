<?php
// Affichage des erreurs sur Hostinger
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Vérifier si l'utilisateur est connecté
if ((!isset($_SESSION['username']) || !isset($_SESSION['password'])) && !isset($_GET['numLicense'])) {
    header("Location: ../Vue/login.php");
    exit();
}

require_once '../Modele/Database.php';
require_once '../Modele/Dao/DaoJoueurs.php';

$daoJoueurs = new \Modele\Dao\DaoJoueurs($_SESSION['username'], $_SESSION['password']);
$numLicense = $_GET['numLicense'];
$joueur = $daoJoueurs->findById($numLicense);

// Vérifier si les modifications ont été soumises
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $joueur->setNom($_POST['nom']);
    $joueur->setPrenom($_POST['prenom']);
    $joueur->setDateNaissance($_POST['dateNaissance']);
    $joueur->setStatut($_POST['statut']);
    $joueur->setCommentaire($_POST['commentaire']);
    $joueur->setTaille($_POST['taille']);
    $joueur->setPoids($_POST['poids']);

    // Mettre à jour le joueur
    $daoJoueurs->update($joueur);

    header("Location: gestionJoueurs.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Modifier Joueur</title>
</head>
<body>

<div class="container">
    <h1 class="form-title">Modifier le Joueur</h1>
    <form method="POST" class="player-form">
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" class="form-input" value="<?php echo htmlspecialchars($joueur->getNom()); ?>" required>
        </div>

        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" class="form-input" value="<?php echo htmlspecialchars($joueur->getPrenom()); ?>" required>
        </div>

        <div class="form-group">
            <label for="dateNaissance">Date de naissance :</label>
            <input type="date" id="dateNaissance" name="dateNaissance" class="form-input" value="<?php echo htmlspecialchars($joueur->getDateNaissance()); ?>" required>
        </div>

        <div class="form-group">
            <label for="statut">Statut :</label>
            <input type="text" id="statut" name="statut" class="form-input" value="<?php echo htmlspecialchars($joueur->getStatut()); ?>" required>
        </div>

        <div class="form-group">
            <label for="commentaire">Commentaire :</label>
            <textarea id="commentaire" name="commentaire" class="form-textarea"><?php echo htmlspecialchars($joueur->getCommentaire()); ?></textarea>
        </div>

        <div class="form-group">
            <label for="taille">Taille (cm) :</label>
            <input type="number" id="taille" name="taille" class="form-input" value="<?php echo htmlspecialchars($joueur->getTaille()); ?>" required>
        </div>

        <div class="form-group">
            <label for="poids">Poids (kg) :</label>
            <input type="number" id="poids" name="poids" class="form-input" value="<?php echo htmlspecialchars($joueur->getPoids()); ?>" required>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn-submit">Valider</button>
            <a href="gestionJoueurs.php" class="btn-cancel"><button type="button">Annuler</button></a>
        </div>
    </form>
</div>


</body>
</html>
