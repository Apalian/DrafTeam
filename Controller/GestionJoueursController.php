<?php

// Affichage des erreurs sur Hostinger
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
    header("Location: ../Vue/Login.php");
    exit();
}

require_once '../Modele/Database.php';
require_once '../Modele/Dao/DaoJoueurs.php';

// Création de l'instance du DAO pour récupérer les joueurs
$daoJoueurs = new \Modele\Dao\DaoJoueurs($_SESSION['username'], $_SESSION['password']);
$joueurs = $daoJoueurs->findAll();  // Récupérer tous les joueurs

// Vérifier si un joueur a été supprimé
if (isset($_GET['delete'])) {
    $numLicense = $_GET['delete'];
    
    // Vérifier si le joueur a participé à des matchs
    if (!$daoJoueurs->hasParticipatedInMatches($numLicense)) {
        $daoJoueurs->delete($numLicense);
        header("Location: GestionJoueursController.php");
        exit();
    } else {
        // Rediriger avec un message d'erreur
        header("Location: GestionJoueursController.php?error=joueur_participe_match");
        exit();
    }
}

// Inclure la vue et transmettre les données à la vue
require_once '../Vue/GestionJoueurs.php';

