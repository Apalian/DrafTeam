function toggleMenu() {
    const sideMenu = document.getElementById('sideMenu');
    const hamburger = document.querySelector('.hamburger');

    sideMenu.classList.toggle('open');
    hamburger.classList.toggle('open');
}
function loadPlayerStats(numLicense) {
    if (!numLicense) {
        console.log("Aucun joueur sélectionné, conteneur vidé.");
        document.getElementById('stats-container').innerHTML = ''; // Vider les stats si aucun joueur sélectionné
        return;
    }

    console.log(`Chargement des statistiques pour le joueur avec numLicense : ${numLicense}`);

    fetch(`index.php?numLicense=${encodeURIComponent(numLicense)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur réseau : ${response.status} - ${response.statusText}`);
            }
            return response.text();
        })
        .then(html => {
            console.log("HTML reçu :", html); // Affiche tout le HTML retourné
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Vérification de l'existence du conteneur dans la réponse
            const stats = doc.querySelector('#stats-container');
            if (stats) {
                console.log("Contenu extrait :", stats.innerHTML); // Logue le contenu extrait
                document.getElementById('stats-container').innerHTML = stats.innerHTML;
            } else {
                console.error("Le conteneur #stats-container est introuvable dans la réponse.");
                document.getElementById('stats-container').innerHTML = '<p>Aucune statistique trouvée.</p>';
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des statistiques :', error);
            document.getElementById('stats-container').innerHTML = '<p>Erreur lors du chargement des statistiques.</p>';
        });
}