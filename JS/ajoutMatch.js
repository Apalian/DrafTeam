/*********************************************
 * ajoutMatch.js
 *********************************************/

// Sélection du conteneur des participations
const participationsContainer = document.getElementById('participations-container');

/**
 * Fonction pour ajouter un bloc de participation
 * (sélection joueur + statut)
 */
function ajouterParticipation() {
    // Créer la div qui contiendra la participation
    const participationDiv = document.createElement('div');
    participationDiv.className = 'participation-group';

    // Injecter le HTML du formulaire de participation
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

    // Ajouter au conteneur principal
    participationsContainer.appendChild(participationDiv);

    // Charger la liste de joueurs dans le <select> nouvellement créé
    const selectJoueur = participationDiv.querySelector('select[name="joueurs[]"]');
    chargerJoueurs(selectJoueur);
}

/**
 * Fonction pour charger la liste des joueurs depuis l'API
 * et remplir le <select> donné en paramètre
 */
async function chargerJoueurs(selectElement) {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            // Si pas de token, on redirige vers la page de login
            window.location.href = '../Vue/Login.html';
            return;
        }

        const response = await fetch('https://drafteamapi.lespi.fr/Joueur/index.php', {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        // data.data est supposé être la liste des joueurs
        if (!data.data || !Array.isArray(data.data)) {
            throw new Error('Réponse inattendue du serveur (liste des joueurs manquante)');
        }

        // Remplir le select avec les joueurs
        data.data.forEach(joueur => {
            const option = document.createElement('option');
            option.value = joueur.numLicense;
            option.textContent = `${joueur.nom} ${joueur.prenom}`;
            selectElement.appendChild(option);
        });
    } catch (error) {
        console.error('Erreur lors du chargement des joueurs :', error);
        alert('Erreur lors du chargement des joueurs.');
    }
}

/**
 * Fonction principale pour ajouter un match + participations
 * Appelée lorsque l'utilisateur soumet le formulaire
 */
async function ajouterMatch(event) {
    event.preventDefault(); // Empêche la soumission classique du formulaire

    try {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '../Vue/Login.html';
            return false;
        }

        // Récupérer les champs du formulaire Match
        // Convertir les scores en entier si remplis, sinon null
        const rawScoreDomicile = document.getElementById('scoreEquipeDomicile').value;
        const rawScoreExterne = document.getElementById('scoreEquipeExterne').value;

        const formData = {
            dateMatch: document.getElementById('dateMatch').value,
            heure: document.getElementById('heure').value,
            nomEquipeAdverse: document.getElementById('nomEquipeAdverse').value.trim(),
            LieuRencontre: document.getElementById('lieuRencontre').value, // Majuscule si le back l'exige
            scoreEquipeDomicile: rawScoreDomicile !== '' ? parseInt(rawScoreDomicile, 10) : null,
            scoreEquipeExterne: rawScoreExterne !== '' ? parseInt(rawScoreExterne, 10) : null,
            participations: []
        };

        // Récupérer toutes les participations (joueurs + statut)
        const joueursSelects = document.querySelectorAll('select[name="joueurs[]"]');
        const statutsSelects = document.querySelectorAll('select[name="statuts[]"]');

        for (let i = 0; i < joueursSelects.length; i++) {
            const numLicense = joueursSelects[i].value;
            const statut = statutsSelects[i].value;

            // On peut vérifier qu'un joueur est bien sélectionné
            if (numLicense === '') {
                alert("Veuillez sélectionner un joueur pour chaque participation.");
                return;
            }

            formData.participations.push({
                numLicense,
                estTitulaire: (statut === 'Titulaire') ? 1 : 0
            });
        }

        console.log('Données envoyées à l’API :', formData);

        // Appel à l'API pour ajouter le match
        const response = await fetch('https://drafteamapi.lespi.fr/Match/index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token}`
            },
            body: JSON.stringify(formData)
        });

        // Si la réponse est un code d'erreur HTTP
        if (!response.ok) {
            // Tenter de lire le JSON d'erreur renvoyé par le serveur
            let errorMsg = `Erreur HTTP! status: ${response.status}`;
            try {
                const errorData = await response.json();
                if (errorData.status_message) {
                    errorMsg = errorData.status_message;
                }
            } catch (parseErr) {
                // On n'a pas pu parser le JSON d'erreur, on garde le message générique
            }
            throw new Error(errorMsg);
        }

        // Succès : on redirige l'utilisateur vers la page de gestion des matchs
        window.location.href = '../Vue/GestionMatchs.html';

    } catch (error) {
        console.error('Erreur lors de l\'ajout du match :', error);
        alert(error.message || 'Erreur lors de l\'ajout du match.');
    }
}

/**
 * Au chargement de la page :
 *  - On ajoute par défaut un bloc de participation vide
 *  - (Optionnel) On pourrait charger d'autres infos si besoin
 */
document.addEventListener('DOMContentLoaded', () => {
    ajouterParticipation();
});
