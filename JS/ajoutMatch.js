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
        <input type="number" name="participations[${participationIndex}][endurance]" class="form-input-add-player" min="0" max="100" step="1" required>

        <label>Vitesse :</label>
        <input type="number" name="participations[${participationIndex}][vitesse]" class="form-input-add-player" min="0" max="100" step="1" required>

        <label>Défense :</label>
        <input type="number" name="participations[${participationIndex}][defense]" class="form-input-add-player" min="0" max="100" step="1" required>

        <label>Tirs :</label>
        <input type="number" name="participations[${participationIndex}][tirs]" class="form-input-add-player" min="0" max="100" step="1" required>

        <label>Passes :</label>
        <input type="number" name="participations[${participationIndex}][passes]" class="form-input-add-player" min="0" max="100" step="1" required>

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
