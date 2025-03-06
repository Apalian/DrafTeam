<?php
// Affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    header("Location: GestionJoueursController.php");
    exit();
}
// Inclure la vue
require_once '../Vue/AjouterJoueur.html';