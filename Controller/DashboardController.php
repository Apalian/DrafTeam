<?php
session_start();

// Vérification de la session
if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
    header("Location: ../Vue/Login.php");
    exit();
}

require_once '../Modele/Database.php';
require_once '../Modele/Dao/DaoJoueurs.php';

// Initialisation du DAO
$daoJoueurs = new \Modele\Dao\DaoJoueurs($_SESSION['username'], $_SESSION['password']);

// Récupération de tous les joueurs
try {
    $joueurs = $daoJoueurs->findAll();
} catch (Exception $e) {
    die('Erreur lors du chargement des joueurs : ' . $e->getMessage());
}

$stats = [];
if (!empty($_GET['numLicense'])) {
    $numLicense = htmlspecialchars($_GET['numLicense']);
    try {
        $stats['postePref'] = $daoJoueurs->getPostePrefere($numLicense);
        $stats['totTitu'] = $daoJoueurs->getTotalTitulaire($numLicense);
        $stats['totRemp'] = $daoJoueurs->getTotalRemplacant($numLicense);
        $stats['pourMatchG'] = $daoJoueurs->getPourcentageMatchsGagnes($numLicense);
        $stats['moyEndurance'] = $daoJoueurs->getMoyenneEndurance($numLicense);
        $stats['moyVitesse'] = $daoJoueurs->getMoyenneVitesse($numLicense);
        $stats['moyDefense'] = $daoJoueurs->getMoyenneDefense($numLicense);
        $stats['moyTirs'] = $daoJoueurs->getMoyenneTirs($numLicense);
        $stats['moyPasses'] = $daoJoueurs->getMoyennePasses($numLicense);
        $stats['selectionConsecutive'] = $daoJoueurs->getSelectionsConsecutives($numLicense);
    } catch (Exception $e) {
        die('Erreur lors du chargement des statistiques : ' . $e->getMessage());
    }
}

// Inclure la vue
include '../Vue/Dashboard.php';
