const participationsContainer = document.getElementById('participations-container');

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
            LieuRencontre: document.getElementById('lieuRencontre').value, // Notez le 'L' majuscule
            scoreEquipeDomicile: document.getElementById('scoreEquipeDomicile').value || null,
            scoreEquipeExterne: document.getElementById('scoreEquipeExterne').value || null
        };
        

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
            const errorData = await response.json();
            throw new Error(errorData.status_message || `HTTP error! status: ${response.status}`);
        }

        // Rediriger vers la page de gestion des matchs
        window.location.href = '../Vue/GestionMatchs.html';
        return false;
    } catch (error) {
        console.error('Erreur lors de l\'ajout du match:', error);
        alert(error.message || 'Erreur lors de l\'ajout du match.');
        return false;
    }
}

// Ajouter un champ de participation au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    ajouterParticipation();
});
