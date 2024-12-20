<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
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
    <title>Ajouter un Match</title>
</head>
<body>
<?php if (isset($errorMessage)): ?>
    <script>
        alert("<?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>");
    </script>
<?php endif; ?>

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
            <select id="lieuRencontre" name="lieuRencontre" class="form-input" required>
                <option value="Domicile">Domicile</option>
                <option value="Extérieur">Extérieur</option>
            </select>
        </div>

        <div class="form-group">
            <label for="scoreEquipeDomicile">Score Équipe Domicile :</label>
            <input type="number" id="scoreEquipeDomicile" name="scoreEquipeDomicile" class="form-input">
        </div>

        <div class="form-group">
            <label for="scoreEquipeExterne">Score Équipe Externe :</label>
            <input type="number" id="scoreEquipeExterne" name="scoreEquipeExterne" class="form-input">
        </div>

        <div class="form-group">
            <h2>Ajouter des Participations</h2>
            <div id="participations-container"></div>
            <button type="button" id="add-participation" class="btn-secondary">Ajouter une Participation</button>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn-submit">Ajouter</button>
            <a href="gestionMatchs.php" class="btn-cancel">Annuler</a>
        </div>
    </form>
</div>

<script>
    const participationsContainer = document.getElementById('participations-container');
    const addParticipationButton = document.getElementById('add-participation');

    const joueurOptions = `<?php foreach ($joueurs as $joueur): ?>
            <option value="<?= htmlspecialchars($joueur->getNumLicense()) ?>">
                <?= htmlspecialchars($joueur->getNumLicense() . ' - ' . $joueur->getNom() . ' ' . $joueur->getPrenom()) ?>
            </option>
        <?php endforeach; ?>`;

    let participationIndex = 0;

    addParticipationButton.addEventListener('click', () => {
        const participationDiv = document.createElement('div');
        participationDiv.classList.add('form-group');
        participationDiv.innerHTML = `
        <h2>Joueur ${participationIndex + 1}</h2>
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

        <label>Endurance :</label>
        <input type="number" name="participations[${participationIndex}][endurance]" class="form-input-add-player" min="0" max="100" step="10" required>

        <label>Vitesse :</label>
        <input type="number" name="participations[${participationIndex}][vitesse]" class="form-input-add-player" min="0" max="100" step="10" required>

        <label>Défense :</label>
        <input type="number" name="participations[${participationIndex}][defense]" class="form-input-add-player" min="0" max="100" step="10" required>

        <label>Tirs :</label>
        <input type="number" name="participations[${participationIndex}][tirs]" class="form-input-add-player" min="0" max="100" step="10" required>

        <label>Passes :</label>
        <input type="number" name="participations[${participationIndex}][passes]" class="form-input-add-player" min="0" max="100" step="10" required>

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
