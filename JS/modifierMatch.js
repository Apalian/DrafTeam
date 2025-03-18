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

        console.log('Loading match details for:', { dateMatch, heure });

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

            // Check if elements exist before setting values
            const elements = {
                dateMatch: document.getElementById('dateMatch'),
                heure: document.getElementById('heure'),
                nomEquipeAdverse: document.getElementById('nomEquipeAdverse'),
                lieuRencontre: document.getElementById('lieuRencontre'),
                scoreEquipeDomicile: document.getElementById('scoreEquipeDomicile'),
                scoreEquipeExterne: document.getElementById('scoreEquipeExterne')
            };

            // Log which elements were not found
            Object.entries(elements).forEach(([key, element]) => {
                if (!element) {
                    console.error(`Element not found: ${key}`);
                }
            });

            // Only set values for elements that exist
            if (elements.dateMatch) elements.dateMatch.value = match.dateMatch;
            if (elements.heure) elements.heure.value = match.heure;
            if (elements.nomEquipeAdverse) elements.nomEquipeAdverse.value = match.nomEquipeAdverse;
            if (elements.lieuRencontre) elements.lieuRencontre.value = match.lieuRencontre;
            if (elements.scoreEquipeDomicile) elements.scoreEquipeDomicile.value = match.scoreEquipeDomicile;
            if (elements.scoreEquipeExterne) elements.scoreEquipeExterne.value = match.scoreEquipeExterne;

            // Load participations after match details are loaded
            await loadParticipations(dateMatch, heure);
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
        const dateMatch = urlParams.get('dateMatch');
        const heure = urlParams.get('heure');

        // Check if we have the required parameters
        if (!dateMatch || !heure) {
            throw new Error('Paramètres dateMatch et heure manquants dans l\'URL');
        }

        // Get form values
        const matchData = {
            dateMatch: dateMatch,  // Use the original dateMatch from URL
            heure: heure,         // Use the original heure from URL
            nomEquipeAdverse: document.getElementById('nomEquipeAdverse').value.trim(),
            LieuRencontre: document.getElementById('lieuRencontre').value,
            scoreEquipeDomicile: document.getElementById('scoreEquipeDomicile').value || null,
            scoreEquipeExterne: document.getElementById('scoreEquipeExterne').value || null
        };

        console.log('Sending match update:', matchData);

        // Update match using PATCH method
        const response = await fetch(`https://drafteamapi.lespi.fr/Match/index.php?dateMatch=${encodeURIComponent(dateMatch)}&heure=${encodeURIComponent(heure)}`, {
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

        // Handle participations
        const joueursSelects = document.querySelectorAll('select[name="joueurs[]"]');
        const statutsSelects = document.querySelectorAll('select[name="statuts[]"]');

        // First, delete existing participations
        const deleteResponse = await fetch(`https://drafteamapi.lespi.fr/Participation/index.php?dateMatch=${encodeURIComponent(dateMatch)}&heure=${encodeURIComponent(heure)}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });

        if (!deleteResponse.ok) {
            console.warn('Warning: Could not delete existing participations');
        }

        // Add new participations
        for (let i = 0; i < joueursSelects.length; i++) {
            const numLicense = joueursSelects[i].value;
            if (!numLicense) continue; // Skip empty selections

            const participationData = {
                numLicense: numLicense,
                dateMatch: dateMatch,
                heure: heure,
                estTitulaire: statutsSelects[i].value === 'Titulaire' ? 1 : 0
            };

            console.log('Adding participation:', participationData);

            const partResponse = await fetch('https://drafteamapi.lespi.fr/Participation/index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(participationData)
            });

            if (!partResponse.ok) {
                const errorData = await partResponse.json();
                console.error('Error adding participation:', errorData);
                throw new Error(errorData.status_message || `Erreur lors de l'ajout de la participation`);
            }
        }

        // If everything succeeded, redirect back to the matches list
        window.location.href = '../Vue/GestionMatchs.html';
        return false;

    } catch (error) {
        console.error('Erreur lors de la modification du match ou des participations :', error);
        alert(error.message || 'Erreur lors de la modification du match/participations.');
        return false;
    }
}