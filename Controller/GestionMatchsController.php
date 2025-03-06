<?php
// Affichage des erreurs sur Hostinger
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../Modele/Database.php';
require_once '../Modele/Dao/DaoMatchs.php';

// Instanciation du DAO et récupération des matchs
$daoMatchs = new \Modele\Dao\DaoMatchs($_SESSION['username'], $_SESSION['password']);
$matchs = null;

// Vérifier si une recherche a été effectuée
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $matchs = $daoMatchs->searchMatches($searchTerm);
}else {
    $matchs = $daoMatchs->findAll();
}

// Vérifier si un Match a été supprimé
if (isset($_GET['dateMatch']) && isset($_GET['heure'])) {
    $dateMatch = $_GET['dateMatch'];
    $heure = $_GET['heure'];
    $daoMatchs->delete($dateMatch, $heure);
    header("Location: GestionMatchsController.php");
    exit;
}

// Inclure la vue et transmettre les données
require_once '../Vue/GestionMatchs.php';
