<?php
// Affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
    header("Location: ../Vue/login.php");
    exit();
}

require_once '../Modele/Database.php';
require_once '../Modele/Dao/DaoJoueurs.php';

$daoJoueurs = new \Modele\Dao\DaoJoueurs($_SESSION['username'], $_SESSION['password']);

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Créer une nouvelle instance de Joueurs
    $nouveauJoueur = new \Modele\Joueurs(
        $_POST['numLicense'],
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['dateNaissance'],
        $_POST['commentaire'],
        $_POST['statut'],
        $_POST['taille'],
        $_POST['poids']
    );

    // Ajouter le nouveau joueur
    $daoJoueurs->create($nouveauJoueur);

    // Rediriger vers la gestion des joueurs
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
    <title>Ajouter un Joueur</title>
</head>
<body>
<div class="container">
    <h1 class="form-title">Ajouter un Nouveau Joueur</h1>
    <form method="POST" class="player-form">
        <div class="form-group">
            <label for="numLicense">Numéro de Licence :</label>
            <input type="text" id="numLicense" name="numLicense" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="dateNaissance">Date de Naissance :</label>
            <input type="date" id="dateNaissance" name="dateNaissance" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="statut">Statut :</label>
            <input type="text" id="statut" name="statut" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="commentaire">Commentaire :</label>
            <textarea id="commentaire" name="commentaire" class="form-textarea"></textarea>
        </div>

        <div class="form-group">
            <label for="taille">Taille (cm) :</label>
            <input type="number" id="taille" name="taille" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="poids">Poids (kg) :</label>
            <input type="number" id="poids" name="poids" class="form-input" required>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn-submit">Ajouter</button>
            <a href="gestionJoueurs.php" class="btn-cancel"><button type="button">Annuler</button></a>
        </div>
    </form>
</div>
</body>
</html>
