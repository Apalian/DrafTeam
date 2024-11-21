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
    // Vérifie les champs requis
    $requiredFields = ['dateMatch', 'heure', 'nomEquipeAdverse', 'lieuRencontre'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            die("Erreur : le champ $field est requis.");
        }
    }

    // Crée une instance de Match
    $nouveauMatch = new \Modele\Matchs(
        $_POST['dateMatch'],
        $_POST['heure'],
        $_POST['nomEquipeAdverse'],
        $_POST['lieuRencontre'],
        $_POST['scoreEquipeDomicile'] ?? null,
        $_POST['scoreEquipeExterne'] ?? null
    );

    // Appelle le DAO pour créer le match
    try {
        $daoMatchs->create($nouveauMatch);
    } catch (Exception $e) {
        die('Erreur lors de l\'ajout du match : ' . $e->getMessage());
    }

    // Traite les participations
    if (!empty($_POST['participations'])) {
        foreach ($_POST['participations'] as $participation) {
            if (
                !empty($participation['numLicense']) &&
                !empty($participation['poste']) &&
                isset($participation['estTitulaire']) &&
                isset($participation['evaluation'])
            ) {
                try {
                    $nouvelleParticipation = new \Modele\Participation(
                        $participation['numLicense'],
                        $_POST['dateMatch'],
                        $_POST['heure'],
                        $participation['estTitulaire'] === 'true',
                        (int)$participation['evaluation'],
                        $participation['poste']
                    );
                    $daoParticipations->create($nouvelleParticipation);
                } catch (Exception $e) {
                    die('Erreur lors de l\'ajout de la participation : ' . $e->getMessage());
                }
            }
        }
    }

    // Redirection après succès
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
            <label for="lieuRencontre">Lieu de la Rencontre :</label>
            <input type="text" id="lieuRencontre" name="lieuRencontre" class="form-input" required>
        </div>

        <div class="form-group">
            <label for="scoreEquipeDomicile">Score Équipe Domicile :</label>
            <input type="number" id="scoreEquipeDomicile" name="scoreEquipeDomicile" class="form-input">
        </div>

        <div class="form-group">
            <label for="scoreEquipeExterne">Score Équipe Adverse :</label>
            <input type="number" id="scoreEquipeExterne" name="scoreEquipeExterne" class="form-input">
        </div>

        <div class="form-group">
            <h2>Ajouter des Participations</h2>
            <div id="participations-container">
                <!-- Les participations seront ajoutées ici dynamiquement -->
            </div>
            <button type="button" id="add-participation" class="btn-secondary">Ajouter une Participation</button>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn-submit">Ajouter</button>
            <a href="gestionMatchs.php" class="btn-cancel"><button type="button">Annuler</button></a>
        </div>
    </form>
</div>

<script>
    const participationsContainer = document.getElementById('participations-container');
    const addParticipationButton = document.getElementById('add-participation');

    addParticipationButton.addEventListener('click', () => {
        const participationDiv = document.createElement('div');
        participationDiv.classList.add('form-group');
        participationDiv.innerHTML = `
            <label>Numéro de Licence :</label>
            <input type="text" name="participations[][numLicense]" class="form-input" required>

            <label>Est Titulaire :</label>
            <select name="participations[][estTitulaire]" class="form-input">
                <option value="true">Oui</option>
                <option value="false">Non</option>
            </select>

            <label>Évaluation :</label>
            <input type="number" name="participations[][evaluation]" class="form-input" required>

            <label>Poste :</label>
            <input type="text" name="participations[][poste]" class="form-input" required>
        `;
        participationsContainer.appendChild(participationDiv);
    });
</script>
</body>
</html>
