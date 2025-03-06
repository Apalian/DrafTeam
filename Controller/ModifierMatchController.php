
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Vérifie si l'utilisateur est connecté
if (!localStorage.getItem('username') || !localStorage.getItem('token')) {
    header("Location: ./Vue/Login.php");
    exit();
}

require_once '../Modele/Database.php';
require_once '../Modele/Dao/DaoMatchs.php';
require_once '../Modele/Dao/DaoParticipation.php';
require_once '../Modele/Dao/DaoJoueurs.php';

$daoMatchs = new \Modele\Dao\DaoMatchs($_SESSION['username'], $_SESSION['password']);
$daoParticipation = new \Modele\Dao\DaoParticipation($_SESSION['username'], $_SESSION['password']);
$daoJoueurs = new \Modele\Dao\DaoJoueurs($_SESSION['username'], $_SESSION['password']);

$dateMatch = $_GET['dateMatch'];
$heure = $_GET['heure'];
$match = $daoMatchs->findById($dateMatch, $heure);
$participations = $daoParticipation->findByMatch($dateMatch, $heure);
$joueurs = $daoJoueurs->findAll();

$errorMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requiredFields = ['lieuRencontre', 'nomEquipeAdverse'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $errorMessage = "Erreur : le champ $field est requis.";
        }
    }

    $scoreEquipeDomicile = $_POST['scoreEquipeDomicile'] !== '' ? $_POST['scoreEquipeDomicile'] : null;
    $scoreEquipeExterne = $_POST['scoreEquipeExterne'] !== '' ? $_POST['scoreEquipeExterne'] : null;

    $match->setLieuRencontre($_POST['lieuRencontre']);
    $match->setNomEquipeAdverse($_POST['nomEquipeAdverse']);
    $match->setScoreEquipeDomicile($scoreEquipeDomicile);
    $match->setScoreEquipeExterne($scoreEquipeExterne);

    // Count the number of titulares
    $titulaireCount = 0;
    if (!empty($_POST['participations'])) {
        foreach ($_POST['participations'] as $pData) {
            if (!empty($pData['numLicense']) && isset($pData['estTitulaire']) && $pData['estTitulaire'] == '1') {
                $titulaireCount++;
            }
        }
    }

    if ($titulaireCount < 7) {
        $errorMessage = "Erreur : Impossible de modifier un match sans au moins 7 joueurs titulaires.";
    }

    if (!$errorMessage) {
        try {
            // Update the match
            $daoMatchs->update($match);

            // Create an associative array of existing participations keyed by numLicense
            $existingParticipations = [];
            foreach ($participations as $part) {
                $existingParticipations[$part->getNumLicense()] = $part;
            }

            // Process submitted participations
            if (!empty($_POST['participations'])) {
                foreach ($_POST['participations'] as $pData) {
                    if (!empty($pData['numLicense']) && !empty($pData['poste'])) {
                        $numLicense = $pData['numLicense'];

                        // Extract skill fields
                        $endurance = isset($pData['endurance']) ? (int)$pData['endurance'] : null;
                        $vitesse = isset($pData['vitesse']) ? (int)$pData['vitesse'] : null;
                        $defense = isset($pData['defense']) ? (int)$pData['defense'] : null;
                        $tirs = isset($pData['tirs']) ? (int)$pData['tirs'] : null;
                        $passes = isset($pData['passes']) ? (int)$pData['passes'] : null;

                        if (isset($existingParticipations[$numLicense])) {
                            // Update existing participation
                            $oldParticipation = $existingParticipations[$numLicense];
                            $oldParticipation->setEstTitulaire($pData['estTitulaire'] == '1');
                            $oldParticipation->setEndurance($endurance);
                            $oldParticipation->setVitesse($vitesse);
                            $oldParticipation->setDefense($defense);
                            $oldParticipation->setTirs($tirs);
                            $oldParticipation->setPasses($passes);
                            $oldParticipation->setPoste($pData['poste']);

                            $daoParticipation->update($oldParticipation);
                            unset($existingParticipations[$numLicense]);
                        } else {
                            // Create new participation
                            $newParticipation = new \Modele\Participation(
                                $numLicense,
                                $dateMatch,
                                $heure,
                                $pData['estTitulaire'] == '1',
                                $endurance,
                                $vitesse,
                                $defense,
                                $tirs,
                                $passes,
                                $pData['poste']
                            );
                            $daoParticipation->create($newParticipation);
                        }
                    }
                }
            }

            // Delete removed participations
            foreach ($existingParticipations as $oldLicense => $oldParticipation) {
                $daoParticipation->delete($oldLicense, $dateMatch, $heure);
            }

            header("Location: GestionMatchsController.php");
            exit();
        } catch (Exception $e) {
            $errorMessage = 'Erreur : ' . $e->getMessage();
        }
    }
}

// Inclure la vue
require_once '../Vue/ModifierMatch.php';