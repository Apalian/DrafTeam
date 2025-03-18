document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const dateMatch = urlParams.get('dateMatch');
    const heure = urlParams.get('heure');

    if (dateMatch && heure) {
        loadMatchDetails(dateMatch, heure);
    } else {
        alert('Paramètres manquants pour charger le match');
        window.location.href = './GestionMatchs.html';
    }

    ajouterParticipation(); // Add an initial participation block
});

async function loadMatchDetails(dateMatch, heure) {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '../Vue/Login.html';
            return;
        }

        console.log('Loading match details for:', dateMatch, heure); // Debug log

        const response = await fetch(`https://drafteamapi.lespi.fr/Match/index.php?dateMatch=${encodeURIComponent(dateMatch)}&heure=${encodeURIComponent(heure)}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        if (!response.ok) {
            throw new Error(`Erreur HTTP! statut: ${response.status}`);
        }

        const data = await response.json();
        console.log('Match data:', data); // Debug log

        if (data.data && data.data.length > 0) {
            const match = data.data[0];

            // Fill in the form with match details
            document.getElementById('dateMatch').value = match.dateMatch;
            document.getElementById('heure').value = match.heure;
            document.getElementById('nomEquipeAdverse').value = match.nomEquipeAdverse;
            document.getElementById('lieuRencontre').value = match.LieuRencontre;
            document.getElementById('scoreEquipeDomicile').value = match.scoreEquipeDomicile;
            document.getElementById('scoreEquipeExterne').value = match.scoreEquipeExterne;

            // After loading match details, load participations
            await loadParticipations(dateMatch, heure);
        } else {
            throw new Error('Match not found');
        }
    } catch (error) {
        console.error('Erreur lors du chargement des détails du match :', error);
        alert('Erreur lors du chargement des détails du match.');
        window.location.href = './GestionMatchs.html';
    }
}

async function loadParticipations(dateMatch, heure) {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '../Vue/Login.html';
            return;
        }

        // Fix: Use the correct endpoint URL with capital 'P' in 'Participation'
        const url = `https://drafteamapi.lespi.fr/Participation/index.php?dateMatch=${encodeURIComponent(dateMatch)}&heure=${encodeURIComponent(heure)}`;
        console.log('Attempting to fetch participations from:', url); // Debug log

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        // Log the response status and URL for debugging
        console.log('Response status:', response.status);
        console.log('Response URL:', response.url);

        if (!response.ok) {
            const errorText = await response.text(); // Get the raw response text
            console.log('Error response:', errorText); // Log the error response
            throw new Error(`Erreur HTTP! statut: ${response.status}`);
        }

        const data = await response.json();
        
        // Clear existing participations
        const container = document.getElementById('participations-container');
        container.innerHTML = '';

        if (data.data && Array.isArray(data.data)) {
            data.data.forEach(participation => {
                ajouterParticipation(participation);
            });
        } else {
            // If no participations found, show a message but don't treat it as an error
            container.innerHTML = '<p>Aucune participation trouvée pour ce match.</p>';
        }

    } catch (error) {
        console.error('Erreur lors du chargement des participations :', error);
        const container = document.getElementById('participations-container');
        container.innerHTML = '<p class="error-message">Erreur lors du chargement des participations.</p>';
    }
}

function ajouterParticipation(participation = {}) {
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
                <option value="Titulaire" ${participation.estTitulaire ? 'selected' : ''}>Titulaire</option>
                <option value="Remplaçant" ${!participation.estTitulaire ? 'selected' : ''}>Remplaçant</option>
            </select>
        </div>
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">Supprimer</button>
    `;

    container.appendChild(participationDiv);

    const selectJoueur = participationDiv.querySelector('select[name="joueurs[]"]');
    chargerJoueurs(selectJoueur, participation.numLicense);
}

async function chargerJoueurs(selectElement, selectedLicense = null) {
    try {
        const token = localStorage.getItem('token');
        const response = await fetch('https://drafteamapi.lespi.fr/Joueur/index.php', {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        if (!response.ok) {
            throw new Error(`Erreur HTTP! statut: ${response.status}`);
        }

        const data = await response.json();
        data.data.forEach(joueur => {
            const option = document.createElement('option');
            option.value = joueur.numLicense;
            option.textContent = `${joueur.nom} ${joueur.prenom}`;
            if (joueur.numLicense === selectedLicense) {
                option.selected = true;
            }
            selectElement.appendChild(option);
        });
    } catch (error) {
        console.error('Erreur lors du chargement des joueurs :', error);
        alert('Erreur lors du chargement des joueurs.');
    }
}

async function modifierMatch(event) {
    event.preventDefault();

    try {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '../Vue/Login.html';
            return;
        }

        const dateMatch = document.getElementById('dateMatch').value;
        const heure = document.getElementById('heure').value;
        const nomEquipeAdverse = document.getElementById('nomEquipeAdverse').value.trim();
        const LieuRencontre = document.getElementById('lieuRencontre').value;
        const scoreEquipeDomicile = document.getElementById('scoreEquipeDomicile').value || null;
        const scoreEquipeExterne = document.getElementById('scoreEquipeExterne').value || null;

        const matchData = {
            dateMatch,
            heure,
            nomEquipeAdverse,
            LieuRencontre,
            scoreEquipeDomicile,
            scoreEquipeExterne
        };

        const response = await fetch('https://drafteamapi.lespi.fr/Match/index.php', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token}`
            },
            body: JSON.stringify(matchData)
        });

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.status_message || `Erreur HTTP lors de la modification du match (code ${response.status})`);
        }

        // Update participations
        const joueursSelects = document.querySelectorAll('select[name="joueurs[]"]');
        const statutsSelects = document.querySelectorAll('select[name="statuts[]"]');

        for (let i = 0; i < joueursSelects.length; i++) {
            const numLicense = joueursSelects[i].value;
            const estTitulaire = statutsSelects[i].value === 'Titulaire' ? 1 : 0;

            const participationData = {
                numLicense,
                dateMatch,
                heure,
                estTitulaire,
                endurance: 0,
                vitesse: 0,
                defense: 0,
                tirs: 0,
                passes: 0,
                poste: null
            };

            const partResp = await fetch('https://drafteamapi.lespi.fr/Participation/index.php', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${token}`
                },
                body: JSON.stringify(participationData)
            });

            if (!partResp.ok) {
                const errorData = await partResp.json();
                throw new Error(errorData.status_message || `Erreur HTTP lors de la modification de la participation (code ${partResp.status})`);
            }
        }

        window.location.href = '../Vue/GestionMatchs.html';
    } catch (error) {
        console.error('Erreur lors de la modification du match ou des participations :', error);
        alert(error.message || 'Erreur lors de la modification du match/participations.');
    }
}