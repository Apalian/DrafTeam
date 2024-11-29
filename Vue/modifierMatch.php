<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['password']) || !isset($_GET['dateMatch']) || !isset($_GET['heure'])) {
    header("Location: ../Vue/login.php");
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

    $titulaireCount = 0;
    if (!empty($_POST['participations'])) {
        foreach ($_POST['participations'] as $participation) {
            if (!empty($participation['numLicense']) && isset($participation['estTitulaire']) && $participation['estTitulaire'] == '1') {
                $titulaireCount++;
            }
        }
    }

    if ($titulaireCount < 7) {
        $errorMessage = "Erreur : Impossible de modifier un match sans au moins 7 joueurs titulaires.";
    }

    if (!$errorMessage) {
        try {
            $daoMatchs->update($match);

            $daoParticipation->deleteByMatch($dateMatch, $heure);
            foreach ($_POST['participations'] as $participation) {
                if (!empty($participation['numLicense']) && !empty($participation['poste'])) {
                    $evaluation = isset($participation['evaluation']) && $participation['evaluation'] !== '' ? (int)$participation['evaluation'] : null;

                    $nouvelleParticipation = new \Modele\Participation(
                        $participation['numLicense'],
                        $dateMatch,
                        $heure,
                        $participation['estTitulaire'] == '1',
                        $evaluation,
                        $participation['poste']
                    );
                    $daoParticipation->create($nouvelleParticipation);
                }
            }

            header("Location: gestionMatchs.php");
            exit();
        } catch (Exception $e) {
            $errorMessage = 'Erreur : ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <title>Modifier Match</title>
</head>
<body>
<?php if (isset($errorMessage)): ?>
    <script>
        alert("<?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>");
    </script>
<?php endif; ?>

<div class="container">
    <h1 class="form-title">Modifier le Match</h1>
    <form method="POST" class="match-form">
        <div class="form-group">
            <label for="dateMatch">Date du Match :</label>
            <input type="date" id="dateMatch" name="dateMatch" class="form-input" value="<?= htmlspecialchars($match->getDateMatch()) ?>" readonly>
        </div>

        <div class="form-group">
            <label for="heure">Heure :</label>
            <input type="time" id="heure" name="heure" class="form-input" value="<?= htmlspecialchars($match->getHeure()) ?>" readonly>
        </div>

        <div class="form-group">
            <label for="nomEquipeAdverse">Nom de l'Équipe Adverse :</label>
            <input type="text" id="nomEquipeAdverse" name="nomEquipeAdverse" class="form-input" value="<?= htmlspecialchars($match->getNomEquipeAdverse()) ?>" required>
        </div>

        <div class="form-group">
            <label for="lieuRencontre">Lieu de la Rencontre :</label>
            <select id="lieuRencontre" name="lieuRencontre" class="form-input" required>
                <option value="Domicile" <?= $match->getLieuRencontre() === 'Domicile' ? 'selected' : '' ?>>Domicile</option>
                <option value="Extérieur" <?= $match->getLieuRencontre() === 'Extérieur' ? 'selected' : '' ?>>Extérieur</option>
            </select>
        </div>

        <div class="form-group">
            <label for="scoreEquipeDomicile">Score Équipe Domicile :</label>
            <input type="number" id="scoreEquipeDomicile" name="scoreEquipeDomicile" class="form-input" value="<?= htmlspecialchars($match->getScoreEquipeDomicile()) ?>">
        </div>

        <div class="form-group">
            <label for="scoreEquipeExterne">Score Équipe Externe :</label>
            <input type="number" id="scoreEquipeExterne" name="scoreEquipeExterne" class="form-input" value="<?= htmlspecialchars($match->getScoreEquipeExterne()) ?>">
        </div>

        <div class="form-group">
            <h2>Modifier les Participations</h2>
            <div id="participations-container">
                <?php foreach ($participations as $index => $participation): ?>
                    <div class="form-group">
                        <label>Numéro de Licence :</label>
                        <input list="joueurs-<?= $index ?>" name="participations[<?= $index ?>][numLicense]" class="form-input-add-player" value="<?= htmlspecialchars($participation->getNumLicense()) ?>" required>
                        <datalist id="joueurs-<?= $index ?>">
                            <?php foreach ($joueurs as $joueur): ?>
                                <option value="<?= htmlspecialchars($joueur->getNumLicense()) ?>"><?= htmlspecialchars($joueur->getNom() . ' ' . $joueur->getPrenom()) ?></option>
                            <?php endforeach; ?>
                        </datalist>

                        <label>Est Titulaire :</label>
                        <select name="participations[<?= $index ?>][estTitulaire]" class="form-input-add-player">
                            <option value="1" <?= $participation->getEstTitulaire() ? 'selected' : '' ?>>Oui</option>
                            <option value="0" <?= !$participation->getEstTitulaire() ? 'selected' : '' ?>>Non</option>
                        </select>

                        <label>Évaluation (0 à 10) :</label>
                        <input type="number" name="participations[<?= $index ?>][evaluation]" class="form-input-add-player" min="0" max="10" value="<?= htmlspecialchars($participation->getEvaluation()) ?>">

                        <label>Poste :</label>
                        <select name="participations[<?= $index ?>][poste]" class="form-input-add-player" required>
                            <option value="Gardien" <?= $participation->getPoste() === 'Gardien' ? 'selected' : '' ?>>Gardien</option>
                            <option value="Pivot" <?= $participation->getPoste() === 'Pivot' ? 'selected' : '' ?>>Pivot</option>
                            <option value="Demi-centre" <?= $participation->getPoste() === 'Demi-centre' ? 'selected' : '' ?>>Demi-centre</option>
                            <option value="Ailier gauche" <?= $participation->getPoste() === 'Ailier gauche' ? 'selected' : '' ?>>Ailier gauche</option>
                            <option value="Ailier droit" <?= $participation->getPoste() === 'Ailier droit' ? 'selected' : '' ?>>Ailier droit</option>
                            <option value="Arrière gauche" <?= $participation->getPoste() === 'Arrière gauche' ? 'selected' : '' ?>>Arrière gauche</option>
                            <option value="Arrière droit" <?= $participation->getPoste() === 'Arrière droit' ? 'selected' : '' ?>>Arrière droit</option>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="add-participation" class="btn-secondary">Ajouter une Participation</button>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn-submit">Valider</button>
            <a href="gestionMatchs.php" class="btn-cancel"><button type="button">Annuler</button></a>
        </div>
    </form>
</div>

<script>
    const participationsContainer = document.getElementById('participations-container');
    const addParticipationButton = document.getElementById('add-participation');

    const joueurOptions = `<?php foreach ($joueurs as $joueur): ?>
        <option value="<?= htmlspecialchars($joueur->getNumLicense()) ?>"><?= htmlspecialchars($joueur->getNom() . ' ' . $joueur->getPrenom()) ?></option>
    <?php endforeach; ?>`;

    let participationIndex = <?= count($participations) ?>;

    addParticipationButton.addEventListener('click', () => {
        const participationDiv = document.createElement('div');
        participationDiv.classList.add('form-group');
        participationDiv.innerHTML = `
            <label>Numéro de Licence :</label>
            <input list="joueurs-${participationIndex}" name="participations[${participationIndex}][numLicense]" class="form-input-add-player" required>
            <datalist id="joueurs-${participationIndex}">
                ${joueurOptions}
            </datalist>

            <label>Est Titulaire :</label>
            <select name="participations[${participationIndex}][estTitulaire]" class="form-input-add-player">
                <option value="1">Oui</option>
                <option value="0">Non</option>
            </select>

            <label>Évaluation (0 à 10) :</label>
            <input type="number" name="participations[${participationIndex}][evaluation]" class="form-input-add-player" min="0" max="10">

            <label>Poste :</label>
            <select name="participations[${participationIndex}][poste]" class="form-input-add-player" required>
                <option value="Gardien">Gardien</option>
                <option value="Pivot">Pivot</option>
                <option value="Demi-centre">Demi-centre</option>
                <option value="Ailier gauche">Ailier gauche</option>
                <option value="Ailier droit">Ailier droit</option>
                <option value="Arrière gauche">Arrière gauche</option>
                <option value="Arrière droit">Arrière droit</option>
            </select>
        `;
        participationsContainer.appendChild(participationDiv);
        participationIndex++;
    });
</script>
</body>
</html>
