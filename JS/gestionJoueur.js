document.addEventListener("DOMContentLoaded", () => {
  const joueursList = document.getElementById("joueurs-list");
  const searchForm = document.getElementById("search-form");
  const searchInput = document.getElementById("search-input");

  async function fetchJoueurs(searchTerm = "") {
    try {
      const token = localStorage.getItem("token");
      if (!token) {
        console.error("Token manquant. Veuillez vous reconnecter.");
        return;
      }

      const response = await fetch(`https://drafteamapi.lespi.fr/Joueur/`, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      if (!response.ok) {
        console.error("Erreur HTTP:", response.status, response.statusText);
        displayJoueurs([]); // Afficher un message d'erreur dans la liste
        return;
      }

      const data = await response.json();
      displayJoueurs(data.data);
    } catch (error) {
      console.error("Erreur lors de la récupération des joueurs:", error);
      displayJoueurs([]); // Afficher un message d'erreur dans la liste
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
                          <p><strong>Statut:</strong> ${joueur.statuts}</p>
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
                      <a href="../Controller/ModifierJoueurController.php?numLicense=${
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
