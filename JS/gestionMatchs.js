// Fonction pour charger les matchs
async function loadMatchs(searchQuery = '') {
    try {
        const username = localStorage.getItem('username');
        const token = localStorage.getItem('token');
        
        if (!username || !token) {
            window.location.href = '../Vue/Login.html';
            return;
        }

        const response = await fetch(`../Controller/GestionMatchsController.php?search=${encodeURIComponent(searchQuery)}&username=${encodeURIComponent(username)}&token=${encodeURIComponent(token)}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const matchs = await response.json();
        displayMatchs(matchs);
    } catch (error) {
        console.error('Erreur lors du chargement des matchs:', error);
        document.getElementById('matchsList').innerHTML = '<p>Erreur lors du chargement des matchs.</p>';
    }
}

// Fonction pour afficher les matchs
function displayMatchs(matchs) {
    const matchsList = document.getElementById('matchsList');
    const template = document.getElementById('matchTemplate');
    
    if (!matchs || matchs.length === 0) {
        matchsList.innerHTML = '<p>Aucun match trouvé.</p>';
        return;
    }

    matchsList.innerHTML = '';
    
    matchs.forEach(match => {
        const clone = template.content.cloneNode(true);
        
        // Remplir les données du match
        clone.querySelector('.date-match').textContent = match.dateMatch;
        clone.querySelector('.heure-match').textContent = match.heure;
        clone.querySelector('.nom-equipe').textContent = match.nomEquipeAdverse;
        clone.querySelector('.lieu-rencontre').textContent = match.lieuRencontre;
        
        // Gérer les scores
        const scoreDomicile = clone.querySelector('.score-domicile');
        const scoreExterne = clone.querySelector('.score-externe');
        
        scoreDomicile.textContent = match.scoreEquipeDomicile === null ? 'pas de score' : match.scoreEquipeDomicile;
        scoreExterne.textContent = match.scoreEquipeExterne === null ? 'pas de score' : match.scoreEquipeExterne;
        
        // Appliquer les classes CSS pour les scores
        scoreDomicile.className = `score-domicile ${getScoreClass(match.scoreEquipeDomicile, match.scoreEquipeExterne, true)}`;
        scoreExterne.className = `score-externe ${getScoreClass(match.scoreEquipeDomicile, match.scoreEquipeExterne, false)}`;
        
        // Gérer le bouton de suppression
        const btnSupprimer = clone.querySelector('.btn-supprimer');
        if (isMatchPassed(match.dateMatch)) {
            btnSupprimer.disabled = true;
            btnSupprimer.title = "Ce match est déjà passé et ne peut pas être supprimé.";
        }
        
        // Stocker les données du match dans les boutons
        btnSupprimer.dataset.dateMatch = match.dateMatch;
        btnSupprimer.dataset.heure = match.heure;
        
        const btnModifier = clone.querySelector('.btn-modifier');
        btnModifier.dataset.dateMatch = match.dateMatch;
        btnModifier.dataset.heure = match.heure;
        
        matchsList.appendChild(clone);
    });
}

// Fonction pour déterminer la classe CSS du score
function getScoreClass(scoreDomicile, scoreExterne, isDomicile) {
    if (scoreDomicile === null || scoreExterne === null) {
        return 'score-unknown';
    }
    
    if (scoreDomicile === scoreExterne) {
        return 'score-gray';
    }
    
    if (isDomicile) {
        return scoreDomicile > scoreExterne ? 'score-green' : 'score-red';
    } else {
        return scoreExterne > scoreDomicile ? 'score-green' : 'score-red';
    }
}

// Fonction pour vérifier si un match est passé
function isMatchPassed(dateMatch) {
    const matchDate = new Date(dateMatch);
    const today = new Date();
    return matchDate < today;
}

// Fonction pour supprimer un match
async function supprimerMatch(button) {
    const dateMatch = button.dataset.dateMatch;
    const heure = button.dataset.heure;
    
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce match ?')) {
        return;
    }

    try {
        const username = localStorage.getItem('username');
        const token = localStorage.getItem('token');
        
        const response = await fetch('../Controller/GestionMatchsController.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                dateMatch,
                heure,
                username,
                token
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Recharger la liste des matchs
        loadMatchs(document.getElementById('searchInput').value);
    } catch (error) {
        console.error('Erreur lors de la suppression du match:', error);
        alert('Erreur lors de la suppression du match.');
    }
}

// Fonction pour modifier un match
function modifierMatch(button) {
    const dateMatch = button.dataset.dateMatch;
    const heure = button.dataset.heure;
    const username = localStorage.getItem('username');
    const token = localStorage.getItem('token');
    
    window.location.href = `../Vue/ModifierMatch.html?dateMatch=${encodeURIComponent(dateMatch)}&heure=${encodeURIComponent(heure)}&username=${encodeURIComponent(username)}&token=${encodeURIComponent(token)}`;
}

// Fonction pour rechercher des matchs
function searchMatches() {
    const searchQuery = document.getElementById('searchInput').value;
    loadMatchs(searchQuery);
}

// Charger les matchs au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    loadMatchs();
}); 