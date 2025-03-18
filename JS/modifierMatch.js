document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const dateMatch = urlParams.get('dateMatch');
    const heure = urlParams.get('heure');

    console.log('URL Parameters:', { dateMatch, heure });

    if (dateMatch && heure) {
        loadMatchDetails(dateMatch, heure);
    } else {
        console.error('Missing required parameters');
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

        // Debug log the parameters
        console.log('Loading match details with params:', { dateMatch, heure });

        const response = await fetch(`https://drafteamapi.lespi.fr/Match/index.php?dateMatch=${encodeURIComponent(dateMatch)}&heure=${encodeURIComponent(heure)}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        if (!response.ok) {
            throw new Error(`Erreur HTTP! statut: ${response.status}`);
        }

        const data = await response.json();
        console.log('Match data received:', data);

        if (data.data && data.data.length > 0) {
            const match = data.data[0];

            // Store original values
            document.getElementById('originalDateMatch').value = match.dateMatch;
            document.getElementById('originalHeure').value = match.heure;

            // Fill in the form
            document.getElementById('dateMatch').value = match.dateMatch;
            document.getElementById('heure').value = match.heure;
            document.getElementById('nomEquipeAdverse').value = match.nomEquipeAdverse;
            document.getElementById('lieuRencontre').value = match.LieuRencontre;
            document.getElementById('scoreEquipeDomicile').value = match.scoreEquipeDomicile;
            document.getElementById('scoreEquipeExterne').value = match.scoreEquipeExterne;

            // Use the match data's date and time for loading participations
            await loadParticipations(match.dateMatch, match.heure);
        } else {
            throw new Error('Match not found');
        }
    } catch (error) {
        console.error('Erreur lors du chargement des détails du match:', error);
        alert('Erreur lors du chargement des détails du match.');
    }
}

async function loadParticipations(dateMatch, heure) {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '../Vue/Login.html';
            return;
        }

        // Debug logs
        console.log('Loading participations for:', { dateMatch, heure });

        // Construct URL with both required parameters
        const url = new URL('https://drafteamapi.lespi.fr/Participation/index.php');
        url.searchParams.append('dateMatch', dateMatch);
        url.searchParams.append('heure', heure);

        console.log('Requesting URL:', url.toString());

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        // Log response details for debugging
        console.log('Response status:', response.status);

        if (!response.ok) {
            // Try to get error details from response
            const errorText = await response.text();
            console.log('Error response:', errorText);
            throw new Error(`Erreur HTTP! statut: ${response.status}`);
        }

        const data = await response.json();
        console.log('Received data:', data);

        // Clear existing participations
        const container = document.getElementById('participations-container');
        container.innerHTML = '';

        if (data.data && Array.isArray(data.data)) {
            data.data.forEach(participation => {
                ajouterParticipation(participation);
            });
        } else {
            container.innerHTML = '<p>Aucune participation trouvée pour ce match.</p>';
        }

    } catch (error) {
        console.error('Erreur détaillée:', error);
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
            return false;
        }

        // Get the original match details from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const originalDateMatch = urlParams.get('dateMatch');
        const originalHeure = urlParams.get('heure');

        if (!originalDateMatch || !originalHeure) {
            throw new Error('Paramètres originaux du match manquants');
        }

        // Get current form values
        const matchData = {
            dateMatch: originalDateMatch,  // Use original date
            heure: originalHeure,         // Use original time
            nomEquipeAdverse: document.getElementById('nomEquipeAdverse').value.trim(),
            LieuRencontre: document.getElementById('lieuRencontre').value,
            scoreEquipeDomicile: document.getElementById('scoreEquipeDomicile').value || null,
            scoreEquipeExterne: document.getElementById('scoreEquipeExterne').value || null
        };

        console.log('Sending match update:', matchData);

        // Update match
        const response = await fetch('https://drafteamapi.lespi.fr/Match/index.php', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
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

        // Delete existing participations first
        await fetch(`https://drafteamapi.lespi.fr/Participation/index.php?dateMatch=${encodeURIComponent(originalDateMatch)}&heure=${encodeURIComponent(originalHeure)}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        // Add new participations
        for (let i = 0; i < joueursSelects.length; i++) {
            const numLicense = joueursSelects[i].value;
            const estTitulaire = statutsSelects[i].value === 'Titulaire' ? 1 : 0;

            if (!numLicense) continue; // Skip empty selections

            const participationData = {
                numLicense,
                dateMatch: originalDateMatch,
                heure: originalHeure,
                estTitulaire,
                endurance: 0,
                vitesse: 0,
                defense: 0,
                tirs: 0,
                passes: 0,
                poste: null
            };

            console.log('Sending participation:', participationData);

            const partResp = await fetch('https://drafteamapi.lespi.fr/Participation/index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(participationData)
            });

            if (!partResp.ok) {
                const errorData = await partResp.json();
                throw new Error(errorData.status_message || `Erreur HTTP lors de l'ajout de la participation (code ${partResp.status})`);
            }
        }

        // Redirect on success
        window.location.href = '../Vue/GestionMatchs.html';
        return false;

    } catch (error) {
        console.error('Erreur lors de la modification du match ou des participations :', error);
        alert(error.message || 'Erreur lors de la modification du match/participations.');
        return false;
    }
}