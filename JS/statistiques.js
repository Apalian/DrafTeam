import { API_CONFIG, fetchWithAuth } from "./common.js";

document.addEventListener("DOMContentLoaded", () => {
  // Vérification de l'authentification
  const token = localStorage.getItem("token");
  if (!token) {
    console.error("Token manquant. Veuillez vous reconnecter.");
    window.location.href = "connexion.html";
    return;
  }

  // Fonction pour charger les statistiques des matchs
  async function loadMatchStats() {
    try {
      const response = await fetchWithAuth(
        `${API_CONFIG.BASE_URL}/Statistiques/index.php`
      );
      const data = await response.json();
      updatePieChart(data.data);
    } catch (error) {
      console.error("Erreur lors de la récupération des statistiques:", error);
      document.querySelector(
        ".content"
      ).innerHTML = `<p style="color: red;">Erreur lors de la récupération des statistiques : ${error.message}</p>`;
    }
  }

  // Fonction pour mettre à jour le graphique en camembert
  function updatePieChart(stats) {
    const data = {
      labels: ["Gagnés", "Perdus", "Nuls"],
      datasets: [
        {
          data: [
            stats.matchsGagnes || 0,
            stats.matchsPerdus || 0,
            stats.matchsNuls || 0,
          ],
          backgroundColor: ["#4CAF50", "#F44336", "#FFC107"],
          hoverOffset: 4,
        },
      ],
    };

    const config = {
      type: "pie",
      data: data,
      options: {
        maintainAspectRatio: false,
        aspectRatio: 1,
      },
    };

    const ctx = document.getElementById("pieChart");
    if (ctx) {
      new Chart(ctx, config);
    }
  }

  // Fonction pour charger la liste des joueurs
  async function loadJoueurs() {
    try {
      const response = await fetchWithAuth(`${API_CONFIG.BASE_URL}/Joueur/`);
      const data = await response.json();
      updateJoueursList(data.data);
    } catch (error) {
      console.error("Erreur lors de la récupération des joueurs:", error);
    }
  }

  // Fonction pour mettre à jour la liste des joueurs dans le select
  function updateJoueursList(joueurs) {
    const select = document.getElementById("numLicense");
    if (!select) return;

    select.innerHTML = '<option value="">-- Sélectionnez un joueur --</option>';
    joueurs.forEach((joueur) => {
      const option = document.createElement("option");
      option.value = joueur.numLicense;
      option.textContent = `${joueur.nom} ${joueur.prenom}`;
      select.appendChild(option);
    });
  }

  // Fonction pour charger les statistiques d'un joueur
  async function loadPlayerStats(numLicense) {
    if (!numLicense) return;

    try {
      const response = await fetchWithAuth(
        `${API_CONFIG.BASE_URL}/Statistiques/index.php?numLicense=${numLicense}`
      );
      const stats = await response.json();
      displayPlayerStats(stats.data);
    } catch (error) {
      console.error("Erreur lors du chargement des statistiques:", error);
    }
  }

  // Fonction pour afficher les statistiques d'un joueur
  function displayPlayerStats(stats) {
    const container = document.getElementById("stats-container");
    if (!container) return;

    container.innerHTML = `
            <h2>Statistiques du joueur</h2>
            <table class="stats-table">
                <thead>
                    <tr>
                        <th>Statistique</th>
                        <th>Valeur</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Poste préféré</td>
                        <td>${stats.postePrefere || "N/A"}</td>
                    </tr>
                    <tr>
                        <td>Nombre total de sélections en tant que titulaire</td>
                        <td>${stats.totalTitulaire || "0"}</td>
                    </tr>
                    <tr>
                        <td>Nombre total de sélections en tant que remplaçant</td>
                        <td>${stats.totalRemplacant || "0"}</td>
                    </tr>
                    <tr>
                        <td>Pourcentage de matchs gagnés</td>
                        <td>${(stats.pourcentageMatchsGagnes || 0).toFixed(
                          2
                        )}%</td>
                    </tr>
                    <tr>
                        <td>Moyenne des évaluations de la vitesse</td>
                        <td>${(stats.moyenneVitesse || 0).toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>Moyenne des évaluations de endurance</td>
                        <td>${(stats.moyenneEndurance || 0).toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>Moyenne des évaluations de la défense</td>
                        <td>${(stats.moyenneDefense || 0).toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>Moyenne des évaluations des tirs</td>
                        <td>${(stats.moyenneTirs || 0).toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>Moyenne des évaluations des passes</td>
                        <td>${(stats.moyennePasses || 0).toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>Nombre de sélections consécutives</td>
                        <td>${stats.selectionsConsecutives || "0"}</td>
                    </tr>
                </tbody>
            </table>
        `;
  }

  // Initialisation
  loadMatchStats();
  loadJoueurs();

  // Gestionnaire d'événement pour le changement de joueur
  const selectJoueur = document.getElementById("numLicense");
  if (selectJoueur) {
    selectJoueur.addEventListener("change", (e) =>
      loadPlayerStats(e.target.value)
    );
  }
});
