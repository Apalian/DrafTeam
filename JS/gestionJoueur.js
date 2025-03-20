import { API_CONFIG, fetchWithAuth } from "./common.js";

document.addEventListener("DOMContentLoaded", () => {
  const joueursList = document.getElementById("joueurs-list");
  const searchForm = document.getElementById("search-form");
  const searchInput = document.getElementById("search-input");

  async function fetchJoueurs() {
    try {
      const response = await fetchWithAuth(`${API_CONFIG.BASE_URL}/Joueur/`);
      const data = await response.json();
      displayJoueurs(data.data);
    } catch (error) {
      console.error("Erreur lors de la récupération des joueurs:", error);
      displayJoueurs([]);
    }
  }

  function displayJoueurs(joueurs) {
    joueursList.innerHTML = "";
    if (joueurs.length === 0) {
      joueursList.innerHTML = "<p>Aucun joueur trouvé.</p>";
      return;
    }

    joueurs.forEach((joueur) => {
      const card = document.createElement("div");
      card.className = "card";
      card.innerHTML = `
                  <div class="card-body">
                      <div class="card-left">
                          <h2>${joueur.nom} ${joueur.prenom}</h2>
                          <p><strong>Numéro de Licence:</strong> ${
                            joueur.numLicense
                          }</p>
                          <p><strong>Date de naissance:</strong> ${
                            joueur.dateNaissance
                          }</p>
                          <p><strong>Statut:</strong> ${joueur.statut}</p>
                          <p><strong>Commentaire:</strong> ${
                            joueur.commentaire
                          }</p>
                      </div>
                      <div class="card-right">
                          <p><strong>Taille:</strong> ${joueur.taille}cm</p>
                          <p><strong>Poids:</strong> ${joueur.poids}kg</p>
                      </div>
                  </div>
                  <div class="card-buttons">
                      <a href="./ModifierJoueur.html?numLicense=${
                        joueur.numLicense
                      }">
                          <button>Modifier</button>
                      </a>
                      <button onclick="confirmDelete('${joueur.numLicense}')" ${
        joueur.hasParticipated
          ? 'disabled title="Ce joueur a participé à des matchs et ne peut pas être supprimé."'
          : ""
      }>Supprimer</button>
                  </div>
              `;
      joueursList.appendChild(card);
    });
  }

  window.confirmDelete = function (numLicense) {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce joueur ?")) {
      console.log(
        `Suppression du joueur avec le numéro de licence: ${numLicense}`
      );
    }
  };

  fetchJoueurs();
});
