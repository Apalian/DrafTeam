document.addEventListener("DOMContentLoaded", () => {
  const joueursList = document.getElementById("joueurs-list");

  async function fetchJoueurs() {
    try {
      const token = localStorage.getItem("token");
      if (!token) {
        console.error("Token manquant. Veuillez vous reconnecter.");
        return;
      }

      const response = await fetchWithAuth(
        `https://drafteamapi.lespi.fr/Joueur/`,
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

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

  window.confirmDelete = async function (numLicense) {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce joueur ?")) {
      try {
        const token = localStorage.getItem("token");
        const response = await fetchWithAuth(
          `https://drafteamapi.lespi.fr/Joueur/index.php?numLicense=${numLicense}`,
          {
            method: "DELETE",
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }
        );

        const data = await response.json();

        if (!response.ok) {
          if (response.status === 403) {
            alert(data.status_message);
            return;
          }
          throw new Error(
            data.status_message || "Erreur lors de la suppression du joueur"
          );
        }

        // Recharger la page pour mettre à jour la liste
        window.location.reload();
      } catch (error) {
        console.error("Erreur:", error);
        alert("Une erreur est survenue lors de la suppression du joueur");
      }
    }
  };

  fetchJoueurs();
});
