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

// Fonction pour ajouter un champ de participation
function ajouterParticipation() {
    const container = document.getElementById('participations-container');
    const participationDiv = document.createElement('div');
    participationDiv.className = 'participation-group';
    
    participationDiv.innerHTML = `
        <div class="form-group">
            <label>Joueur :</label>
            <select name="joueurs[]" class="form-input" required>
                <option value="">Sélectionner un joueur</option>
            </select>
        </div>
        <div class="form-group">
            <label>Statut :</label>
            <select name="statuts[]" class="form-input" required>
                <option value="Titulaire">Titulaire</option>
                <option value="Remplaçant">Remplaçant</option>
            </select>
        </div>
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">Supprimer</button>
    `;
    
    container.appendChild(participationDiv);
    chargerJoueurs(participationDiv.querySelector('select[name="joueurs[]"]'));
}

// Fonction pour charger la liste des joueurs dans un select
async function chargerJoueurs(selectElement) {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '../Vue/Login.html';
            return;
        }

        const response = await fetch('https://drafteamapi.lespi.fr/Joueur/index.php', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        data.data.forEach(joueur => {
            const option = document.createElement('option');
            option.value = joueur.numLicense;
            option.textContent = `${joueur.nom} ${joueur.prenom}`;
            selectElement.appendChild(option);
        });
    } catch (error) {
        console.error('Erreur lors du chargement des joueurs:', error);
        alert('Erreur lors du chargement des joueurs.');
    }
}

// Fonction pour ajouter un match
async function ajouterMatch(event) {
    event.preventDefault();
    
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '../Vue/Login.html';
            return false;
        }

        // Récupérer les données du formulaire
        const formData = {
            dateMatch: document.getElementById('dateMatch').value,
            heure: document.getElementById('heure').value,
            nomEquipeAdverse: document.getElementById('nomEquipeAdverse').value,
            lieuRencontre: document.getElementById('lieuRencontre').value,
            scoreEquipeDomicile: document.getElementById('scoreEquipeDomicile').value || null,
            scoreEquipeExterne: document.getElementById('scoreEquipeExterne').value || null,
            participations: []
        };

        // Récupérer les participations
        const joueursSelects = document.querySelectorAll('select[name="joueurs[]"]');
        const statutsSelects = document.querySelectorAll('select[name="statuts[]"]');
        
        for (let i = 0; i < joueursSelects.length; i++) {
            formData.participations.push({
                numLicense: joueursSelects[i].value,
                statut: statutsSelects[i].value
            });
        }

        // Envoyer les données à l'API
        const response = await fetch('https://drafteamapi.lespi.fr/Match/index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(formData)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Rediriger vers la page de gestion des matchs
        window.location.href = '../Vue/GestionMatchs.html';
        return false;
    } catch (error) {
        console.error('Erreur lors de l\'ajout du match:', error);
        alert('Erreur lors de l\'ajout du match.');
        return false;
    }
}

// Ajouter un champ de participation au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    ajouterParticipation();
});
