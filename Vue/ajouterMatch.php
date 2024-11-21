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

    // Liste des joueurs récupérés dynamiquement via PHP
    const joueurs = <?php echo json_encode(
        array_map(function ($joueur) {
            return [
                'numLicense' => $joueur->getNumLicense(),
                'nom' => $joueur->getNom(),
                'prenom' => $joueur->getPrenom(),
            ];
        }, $joueurs)
    ); ?>;

    addParticipationButton.addEventListener('click', () => {
        const participationDiv = document.createElement('div');
        participationDiv.classList.add('form-group');

        // Crée un input avec un datalist pour la barre de recherche
        const datalistId = `joueurs-${Date.now()}`;
        const datalistOptions = joueurs
            .map(
                joueur =>
                    `<option value="${joueur.numLicense}">${joueur.numLicense} - ${joueur.nom} ${joueur.prenom}</option>`
            )
            .join('');

        participationDiv.innerHTML = `
            <label>Numéro de Licence :</label>
            <input list="${datalistId}" name="participations[][numLicense]" class="form-input" required>
            <datalist id="${datalistId}">
                ${datalistOptions}
            </datalist>

            <label>Est Titulaire :</label>
            <select name="participations[][estTitulaire]" class="form-input">
                <option value="true">Oui</option>
                <option value="false">Non</option>
            </select>

            <label>Évaluation (0 à 10) :</label>
            <input type="number" name="participations[][evaluation]" class="form-input" min="0" max="10" required>

            <label>Poste :</label>
            <select name="participations[][poste]" class="form-input">
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
    });
</script>
</body>
</html>
