/**
 * Sélection du conteneur des participations
 */
const participationsContainer = document.getElementById(
  "participations-container"
);

/**
 * Fonction pour ajouter un bloc de participation
 */
function ajouterParticipation() {
  const participationDiv = document.createElement("div");
  participationDiv.className = "participation-group";

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

  participationsContainer.appendChild(participationDiv);

  // Charger la liste des joueurs dans ce select
  const selectJoueur = participationDiv.querySelector(
    'select[name="joueurs[]"]'
  );
  chargerJoueurs(selectJoueur);
}

/**
 * Charger la liste des joueurs dans le <select> donné
 */
async function chargerJoueurs(selectElement) {
  try {
    const token = localStorage.getItem("token");
    if (!token) {
      window.location.href = "../Vue/Login.html";
      return;
    }

    const response = await fetchWithAuth(
      "https://drafteamapi.lespi.fr/Joueur/index.php",
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    );

    if (!response.ok) {
      throw new Error(`Erreur HTTP! statut: ${response.status}`);
    }

    const data = await response.json();
    if (!data.data || !Array.isArray(data.data)) {
      throw new Error("Réponse inattendue de l’API Joueur");
    }

    // Remplir le <select> avec la liste des joueurs
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
 * Fonction appelée au submit pour ajouter le match
 * puis les participations (chacune séparément).
 */
async function ajouterMatch(event) {
  event.preventDefault(); // Empêche la soumission classique

  try {
    // Vérif token
    const token = localStorage.getItem("token");
    if (!token) {
      window.location.href = "../Vue/Login.html";
      return;
    }

    // Préparer les données du match
    const dateMatch = document.getElementById("dateMatch").value;
    const heure = document.getElementById("heure").value;
    const nomEquipeAdverse = document
      .getElementById("nomEquipeAdverse")
      .value.trim();
    const LieuRencontre = document.getElementById("lieuRencontre").value; // Domicile / Extérieur
    const rawScoreDomicile = document.getElementById(
      "scoreEquipeDomicile"
    ).value;
    const rawScoreExterne = document.getElementById("scoreEquipeExterne").value;

    // Convertir en entier ou null
    const scoreEquipeDomicile =
      rawScoreDomicile !== "" ? parseInt(rawScoreDomicile, 10) : null;
    const scoreEquipeExterne =
      rawScoreExterne !== "" ? parseInt(rawScoreExterne, 10) : null;

    // Construire l'objet "match" à envoyer
    const matchData = {
      nomEquipeAdverse,
      LieuRencontre,
      scoreEquipeDomicile,
      scoreEquipeExterne,
    };

    // 1) -- APPEL API POUR CRÉER LE MATCH --
    console.log("Envoi du match :", matchData);

    let response = await fetchWithAuth(
      `https://drafteamapi.lespi.fr/Match/index.php?dateMatch=${dateMatch}&heure=${heure}`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(matchData),
      }
    );

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      const errorMsg =
        errorData.status_message ||
        `Erreur HTTP lors de l'ajout du match (code ${response.status})`;
      throw new Error(errorMsg);
    }

    // 2) -- ENSUITE, INSÉRER CHAQUE PARTICIPATION INDIVIDUELLEMENT --
    // Récupération des sélections de joueurs + statuts
    const joueursSelects = document.querySelectorAll(
      'select[name="joueurs[]"]'
    );
    const statutsSelects = document.querySelectorAll(
      'select[name="statuts[]"]'
    );

    // Boucle sur toutes les participations
    for (let i = 0; i < joueursSelects.length; i++) {
      const numLicense = joueursSelects[i].value;
      const statut = statutsSelects[i].value; // "Titulaire" ou "Remplaçant"

      // Si le champ joueur n’est pas sélectionné
      if (!numLicense) {
        alert("Veuillez sélectionner un joueur pour chaque participation.");
        return;
      }

      // Convertir ce statut en un int non-vide (1 ou 2) pour contourner empty(0) en PHP
      const estTitulaire = statut === "Titulaire" ? 1 : 0;

      // On n’a pas d’autres champs (endurance, vitesse, etc.) => on met 0 ou null
      const participationBody = {
        estTitulaire,
        endurance: 0,
        vitesse: 0,
        defense: 0,
        tirs: 0,
        passes: 0,
        poste: null,
      };

      console.log("Envoi participation :", participationBody);

      let partResp = await fetchWithAuth(
        `https://drafteamapi.lespi.fr/Participation/index.php?numLicense=${encodeURIComponent(
          numLicense
        )}&dateMatch=${encodeURIComponent(
          dateMatch
        )}&heure=${encodeURIComponent(heure)}`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
          body: JSON.stringify(participationBody),
        }
      );

      if (!partResp.ok) {
        const errorData = await partResp.json().catch(() => ({}));
        const errorMsg =
          errorData.status_message ||
          `Erreur HTTP lors de l'ajout de la participation (code ${partResp.status})`;
        throw new Error(errorMsg);
      }
    }
    // Rediriger
    alert("Ajout du match et des participations réussis");
    window.location.href = "./GestionMatch.html";
  } catch (error) {
    console.error(
      "Erreur lors de l’ajout du match ou des participations :",
      error
    );
    alert(error.message || "Erreur lors de l’ajout du match/participations.");
  }
}

/**
 * Au chargement : ajouter un premier bloc de participation
 */
document.addEventListener("DOMContentLoaded", () => {
  ajouterParticipation();
});
