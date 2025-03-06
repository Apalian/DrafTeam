<?php
// Affichage des erreurs sur Hostinger
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifie si l'utilisateur est connecté
if (!localStorage.getItem('username') || !localStorage.getItem('token')) {
    header("Location: ./Vue/Login.php");
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

    header("Location: GestionJoueursController.php");
    exit();
}

// Inclure la vue
require_once '../Vue/ModifierJoueur.php';