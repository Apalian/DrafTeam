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
    <h1>Modifier le Joueur</h1>
    <form method="POST">
        <div class="card">
            <div class="card-body">
                <div class="form">
                    <label>Nom :</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($joueur->getNom()); ?>" required>
                    <label>Prénom :</label>
                    <input type="text" name="prenom" value="<?php echo htmlspecialchars($joueur->getPrenom()); ?>" required>
                    <label>Date de naissance :</label>
                    <input type="date" name="dateNaissance" value="<?php echo htmlspecialchars($joueur->getDateNaissance()); ?>" required>
                    <label>Statut :</label>
                    <input type="text" name="statut" value="<?php echo htmlspecialchars($joueur->getStatut()); ?>" required>
                    <label>Commentaire :</label>
                    <textarea name="commentaire"><?php echo htmlspecialchars($joueur->getCommentaire()); ?></textarea>
                    <label>Taille (cm) :</label>
                    <input type="number" name="taille" value="<?php echo htmlspecialchars($joueur->getTaille()); ?>" >
                    <label>Poids (kg) :</label>
                    <input type="number" name="poids" value="<?php echo htmlspecialchars($joueur->getPoids()); ?>" >
                </div>
            </div>
            <!-- Boutons Valider et Annuler -->
            <div class="card-buttons">
                <button type="submit">Valider</button>
                <a href="gestionJoueurs.php"><button type="button">Annuler</button></a>
            </div>
        </div>
    </form>
</div>

</body>
</html>
