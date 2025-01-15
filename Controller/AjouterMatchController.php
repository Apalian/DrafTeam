<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
    header("Location: ../Vue/Login.php");
    exit();
}

require_once '../Modele/Database.php';
require_once '../Modele/Dao/DaoMatchs.php';
require_once '../Modele/Dao/DaoParticipation.php';
require_once '../Modele/Dao/DaoJoueurs.php';

$daoMatchs = new \Modele\Dao\DaoMatchs($_SESSION['username'], $_SESSION['password']);
$daoParticipation = new \Modele\Dao\DaoParticipation($_SESSION['username'], $_SESSION['password']);
$daoJoueurs = new \Modele\Dao\DaoJoueurs($_SESSION['username'], $_SESSION['password']);

$joueurs = $daoJoueurs->findAll();

$errorMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requiredFields = ['dateMatch', 'heure', 'nomEquipeAdverse', 'lieuRencontre'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errorMessage = "Erreur : le champ $field est requis.";
        }
    }

    $scoreEquipeDomicile = $_POST['scoreEquipeDomicile'] !== '' ? $_POST['scoreEquipeDomicile'] : null;
    $scoreEquipeExterne = $_POST['scoreEquipeExterne'] !== '' ? $_POST['scoreEquipeExterne'] : null;

    $nouveauMatch = new \Modele\Matchs(
        $_POST['dateMatch'],
        $_POST['heure'],
        $_POST['nomEquipeAdverse'],
        $_POST['lieuRencontre'],
        $scoreEquipeDomicile,
        $scoreEquipeExterne
    );

    // Vérification des joueurs titulaires
    $titulaireCount = 0;
    if (!empty($_POST['participations'])) {
        foreach ($_POST['participations'] as $participation) {
            if (!empty($participation['numLicense']) && isset($participation['estTitulaire']) && $participation['estTitulaire'] == '1') {
                $titulaireCount++;
            }
        }
    }

    if ($titulaireCount < 7) {
        $errorMessage = "Erreur : Impossible d'ajouter un match sans au moins 7 joueurs titulaires.";
    }

    if (!$errorMessage) {
        try {
            $daoMatchs->create($nouveauMatch);

            foreach ($_POST['participations'] as $participation) {
                if (!empty($participation['numLicense']) && !empty($participation['poste'])) {
                    $nouvelleParticipation = new \Modele\Participation(
                        $participation['numLicense'],
                        $_POST['dateMatch'],
                        $_POST['heure'],
                        $participation['estTitulaire'] == '1',
                        $participation['endurance'] ?? null,
                        $participation['vitesse'] ?? null,
                        $participation['defense'] ?? null,
                        $participation['tirs'] ?? null,
                        $participation['passes'] ?? null,
                        $participation['poste']
                    );
                    $daoParticipation->create($nouvelleParticipation);
                }
            }

            header("Location: GestionMatchsController.php");
            exit();
        } catch (Exception $e) {
            $errorMessage = 'Erreur : ' . $e->getMessage();
        }
    }
}

include '../Vue/ajouterMatchVue.php';

