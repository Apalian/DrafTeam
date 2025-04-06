/**
 * Sélection du conteneur des participations
 */
const participationsContainer = document.getElementById("participations-container");

/**
 * Fonction pour ajouter un bloc de participation
 */
function ajouterParticipation() {
  const participationDiv = document.createElement("div");
  participationDiv.className = "participation-group";

  participationDiv.innerHTML = `
    <div class="form-group">
        <label>Joueur :</label>
        <select name="joueurs[]" class="form-input">
            <option value="">Sélectionner un joueur</option>
        </select>
    </div>
    <div class="form-group">
        <label>Statut :</label>
        <select name="statuts[]" class="form-input">
            <option value="Titulaire">Titulaire</option>
            <option value="Remplaçant">Remplaçant</option>
        </select>
    </div>
    <button type="button" class="btn-remove" onclick="this.parentElement.remove()">Supprimer</button>
  `;

  participationsContainer.appendChild(participationDiv);

  // Charger les joueurs dans le <select>
  const selectJoueur = participationDiv.querySelector('select[name="joueurs[]"]');
  chargerJoueurs(selectJoueur);
}

/**
 * Charger la liste des joueurs dans un <select>
 */
async function chargerJoueurs(selectElement) {
  try {
    const token = localStorage.getItem("token");
    if (!token) {
      window.location.href = "../Vue/Login.html";
      return;
    }

    const response = await fetchWithAuth("https://drafteamapi.lespi.fr/Joueur/index.php", {
      headers: { Authorization: `Bearer ${token}` },
    });

    if (!response.ok) {
      throw new Error(`Erreur HTTP! statut: ${response.status}`);
    }

    const data = await response.json();
    if (!data.data || !Array.isArray(data.data)) {
      throw new Error("Réponse inattendue de l’API Joueur");
    }

    data.data.forEach((joueur) => {
      const option = document.createElement("option");
      option.value = joueur.numLicense;
      option.textContent = `${joueur.nom} ${joueur.prenom}`;
      selectElement.appendChild(option);
    });
  } catch (error) {
    console.error("Erreur lors du chargement des joueurs :", error);
    alert("Erreur lors du chargement des joueurs.");
  }
}

/**
 * Soumission du formulaire : ajout du match + participations si présentes
 */
async function ajouterMatch(event) {
  event.preventDefault();

  try {
    const token = localStorage.getItem("token");
    if (!token) {
      window.location.href = "../Vue/Login.html";
      return;
    }

    // Récupération des données du formulaire
    const dateMatch = document.getElementById("dateMatch").value;
    const heure = document.getElementById("heure").value;
    const nomEquipeAdverse = document.getElementById("nomEquipeAdverse").value.trim();
    const lieuRencontre = document.getElementById("lieuRencontre").value;
    const scoreEquipeDomicile = document.getElementById("scoreEquipeDomicile").value || null;
    const scoreEquipeExterne = document.getElementById("scoreEquipeExterne").value || null;

    const matchData = {
      nomEquipeAdverse,
      LieuRencontre: lieuRencontre,
      scoreEquipeDomicile: scoreEquipeDomicile !== "" ? parseInt(scoreEquipeDomicile) : null,
      scoreEquipeExterne: scoreEquipeExterne !== "" ? parseInt(scoreEquipeExterne) : null,
    };

    // Création du match
    console.log("Envoi du match :", matchData);

    const matchResponse = await fetchWithAuth(
      `https://drafteamapi.lespi.fr/Match/index.php?dateMatch=${encodeURIComponent(dateMatch)}&heure=${encodeURIComponent(heure)}`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(matchData),
      }
    );

    if (!matchResponse.ok) {
      const errorData = await matchResponse.json().catch(() => ({}));
      const errorMsg = errorData.status_message || `Erreur HTTP lors de l'ajout du match (code ${matchResponse.status})`;
      throw new Error(errorMsg);
    }

    // Préparation des participations valides
    const joueursSelects = document.querySelectorAll('select[name="joueurs[]"]');
    const statutsSelects = document.querySelectorAll('select[name="statuts[]"]');

    let participationsValides = [];

    for (let i = 0; i < joueursSelects.length; i++) {
      const numLicense = joueursSelects[i].value;
      const statut = statutsSelects[i].value;

      if (!numLicense) continue; // On ignore les participations vides

      participationsValides.push({
        numLicense,
        body: {
          estTitulaire: statut === "Titulaire" ? 1 : 0,
          endurance: 0,
          vitesse: 0,
          defense: 0,
          tirs: 0,
          passes: 0,
          poste: null,
        },
      });
    }

    // Envoi des participations valides
    for (const participation of participationsValides) {
      console.log("Envoi participation :", participation);

      const partResp = await fetchWithAuth(
        `https://drafteamapi.lespi.fr/Participation/index.php?numLicense=${encodeURIComponent(participation.numLicense)}&dateMatch=${encodeURIComponent(dateMatch)}&heure=${encodeURIComponent(heure)}`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
          body: JSON.stringify(participation.body),
        }
      );

      if (!partResp.ok) {
        const errorData = await partResp.json().catch(() => ({}));
        const errorMsg = errorData.status_message || `Erreur HTTP lors de l'ajout de la participation (code ${partResp.status})`;
        throw new Error(errorMsg);
      }
    }

    alert("Ajout du match " + (participationsValides.length ? "et des participations " : "") + "réussi !");
    window.location.href = "../Vue/GestionMatchs.html";
  } catch (error) {
    console.error("Erreur lors de l’ajout du match ou des participations :", error);
    alert(error.message || "Erreur lors de l’ajout du match/participations.");
  }
}

// Ajouter un bloc de participation par défaut au chargement
document.addEventListener("DOMContentLoaded", () => {
  ajouterParticipation(); // Tu peux commenter cette ligne si tu veux commencer avec 0 participation
});
