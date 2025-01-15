<?php
// Affichage des erreurs sur Hostinger
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
    header("Location: ../Vue/Login.php");
    exit();
}

require_once '../Modele/Database.php';
require_once '../Modele/Dao/DaoMatchs.php';

// Instanciation du DAO et récupération des matchs
$daoMatchs = new \Modele\Dao\DaoMatchs($_SESSION['username'], $_SESSION['password']);
$matchs = $daoMatchs->findAll();

// Vérifier si un Match a été supprimé
if (isset($_GET['dateMatch']) && isset($_GET['heure'])) {
    $dateMatch = $_GET['dateMatch'];
    $heure = $_GET['heure'];
    $daoMatchs->delete($dateMatch, $heure);
    header("Location: GestionMatchs.php");
    exit;
}

// Inclure la vue et transmettre les données
require_once '../Vue/GestionMatchs.php';
