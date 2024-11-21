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
require_once '../Modele/Dao/DaoMatchs.php';
require_once '../Modele/Dao/DaoParticipation.php';

$daoMatchs = new \Modele\Dao\DaoMatchs($_SESSION['username'], $_SESSION['password']);
$daoParticipations = new \Modele\Dao\DaoParticipation($_SESSION['username'], $_SESSION['password']);

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Créer une nouvelle instance de Matchs
    $nouveauMatch = new \Modele\Matchs(
        $_POST['dateMatch'],
        $_POST['heure'],
        $_POST['nomEquipeAdverse'],
        $_POST['lieuRencontre'],
        $_POST['scoreEquipeDomicile'] ?? null,
        $_POST['scoreEquipeExterne'] ?? null
    );

    // Ajouter le nouveau match
    $daoMatchs->create($nouveauMatch);

    // Ajouter les participations des joueurs
    if (!empty($_POST['participations'])) {
        foreach ($_POST['participations'] as $participation) {
            $nouvelleParticipation = new \Modele\Participation(
                $participation['numLicense'],
                $_POST['dateMatch'],
                $_POST['heure'],
                $participation['estTitulaire'] === 'true',
                $participation['evaluation'],
                $participation['poste']
            );
            $daoParticipations->create($nouvelleParticipation);
        }
    }

    // Rediriger vers la gestion des matchs
    header("Location: gestionMatchs.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Ajouter un Match</title>
</head>
<body>
<div class="container">
    <h1 class="form-title">Ajouter un Nouveau Match</h1>
    <form method="POST" class="match-form">
        <div class="form-group">
            <label for="dateMatch">Date du Match :</label>
            <input type="date" id="dateMatch" name="dateMatch" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="heure">Heure :</label>
            <input type="time" id="heure" name="heure" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="nomEquipeAdverse">Nom de l'Équipe Adverse :</label>
            <input type="text" id="nomEquipeAdverse" name="nomEquipeAdverse" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="lieuRen
