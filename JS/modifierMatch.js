let existingParticipations = [];

document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const dateMatch = urlParams.get("dateMatch");
  const heure = urlParams.get("heure");

  console.log("URL Parameters:", { dateMatch, heure });

  if (dateMatch && heure) {
    loadMatchDetails(dateMatch, heure);
  } else {
    console.error("Missing required parameters");
    alert("Paramètres manquants pour charger le match");
    window.location.href = "./GestionMatchs.html";
  }

  // Ajoute un premier bloc vide
  ajouterParticipation();
});

/**
 * Charge les détails d'un match et ses participations
 */
async function loadMatchDetails(dateMatch, heure) {
  try {
    const token = localStorage.getItem("token");
    if (!token) {
      window.location.href = "../Vue/Login.html";
      return;
    }

    console.log("Loading match details for:", { dateMatch, heure });

    // Récupération du match (en GET)
    const response = await fetch(
      `https://drafteamapi.lespi.fr/Match/index.php?dateMatch=${encodeURIComponent(
        dateMatch
      )}&heure=${encodeURIComponent(heure)}`,
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );

    if (!response.ok) {
      if (response.status === 401) {
        logout();
      }
      throw new Error(`Erreur HTTP! statut: ${response.status}`);
    }

    const data = await response.json();
    console.log("Match data received:", data);

    if (data.data && data.data.length > 0) {
      const match = data.data[0];

      // Remplit le formulaire
      const elements = {
        dateMatch: document.getElementById("dateMatch"),
        heure: document.getElementById("heure"),
        nomEquipeAdverse: document.getElementById("nomEquipeAdverse"),
        lieuRencontre: document.getElementById("lieuRencontre"),
        scoreEquipeDomicile: document.getElementById("scoreEquipeDomicile"),
        scoreEquipeExterne: document.getElementById("scoreEquipeExterne"),
      };

      Object.entries(elements).forEach(([key, element]) => {
        if (!element) {
          console.error(`Element not found: ${key}`);
        }
      });

      if (elements.dateMatch) elements.dateMatch.value = match.dateMatch;
      if (elements.heure) elements.heure.value = match.heure;
      if (elements.nomEquipeAdverse)
        elements.nomEquipeAdverse.value = match.nomEquipeAdverse;
      if (elements.lieuRencontre)
        elements.lieuRencontre.value = match.lieuRencontre;
      if (elements.scoreEquipeDomicile)
        elements.scoreEquipeDomicile.value = match.scoreEquipeDomicile;
      if (elements.scoreEquipeExterne)
        elements.scoreEquipeExterne.value = match.scoreEquipeExterne;

      // Charge aussi les participations
      await loadParticipations(dateMatch, heure);
    } else {
      throw new Error("Match not found");
    }
  } catch (error) {
    console.error("Erreur lors du chargement des détails du match:", error);
    alert("Erreur lors du chargement des détails du match.");
  }
}

/**
 * Charge les participations du match et les affiche
 */
async function loadParticipations(dateMatch, heure) {
  try {
    const token = localStorage.getItem("token");
    if (!token) {
      window.location.href = "../Vue/Login.html";
      return;
    }

    console.log("Loading participations for:", { dateMatch, heure });

    // Construct URL with both required parameters
    const url = new URL("https://drafteamapi.lespi.fr/Participation/index.php");
    url.searchParams.append("dateMatch", dateMatch);
    url.searchParams.append("heure", heure);

    console.log("Requesting URL:", url.toString());

    const response = await fetch(url, {
      method: "GET",
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    });

    console.log("Response status:", response.status);

    if (!response.ok) {
      if (response.status === 401) {
        logout();
      }
      const errorText = await response.text();
      console.log("Error response:", errorText);
      throw new Error(`Erreur HTTP! statut: ${response.status}`);
    }

    const data = await response.json();
    console.log("Received data:", data);

    // Vider le container
    const container = document.getElementById("participations-container");
    container.innerHTML = "";

    // CHANGEMENT : on stocke les participations existantes globalement
    existingParticipations = Array.isArray(data.data) ? data.data : [];

    if (existingParticipations.length > 0) {
      existingParticipations.forEach((participation) => {
        ajouterParticipation(participation);
      });
    } else {
      container.innerHTML =
        "<p>Aucune participation trouvée pour ce match.</p>";
    }
  } catch (error) {
    console.error("Erreur détaillée:", error);
    const container = document.getElementById("participations-container");
    container.innerHTML =
      '<p class="error-message">Erreur lors du chargement des participations.</p>';
  }
}

/**
 * Ajoute un bloc de participation dans le DOM
 * @param {*} participation (facultatif) données existantes (numLicense, estTitulaire, etc.)
 */
function ajouterParticipation(participation = {}) {
  const container = document.getElementById("participations-container");
  const participationDiv = document.createElement("div");
  participationDiv.className = "participation-group";

  // participation.estTitulaire = 1 => Titulaire, 0 => Remplaçant
  const isTitulaire = participation.estTitulaire ? "Titulaire" : "Remplaçant";

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
                <option value="Titulaire"   ${
                  isTitulaire === "Titulaire" ? "selected" : ""
                }>Titulaire</option>
                <option value="Remplaçant" ${
                  isTitulaire === "Remplaçant" ? "selected" : ""
                }>Remplaçant</option>
            </select>
        </div>
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">Supprimer</button>
    `;

  container.appendChild(participationDiv);

  // Charge la liste des joueurs (et sélectionne si participation.numLicense existe)
  const selectJoueur = participationDiv.querySelector(
    'select[name="joueurs[]"]'
  );
  chargerJoueurs(selectJoueur, participation.numLicense);
}

/**
 * Charge la liste des joueurs dans la <select>
 */
async function chargerJoueurs(selectElement, selectedLicense = null) {
  try {
    const token = localStorage.getItem("token");
    const response = await fetch(
      "https://drafteamapi.lespi.fr/Joueur/index.php",
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );

    if (!response.ok) {
      if (response.status === 401) {
        logout();
      }
      throw new Error(`Erreur HTTP! statut: ${response.status}`);
    }

    const data = await response.json();
    data.data.forEach((joueur) => {
      const option = document.createElement("option");
      option.value = joueur.numLicense;
      option.textContent = `${joueur.nom} ${joueur.prenom}`;
      if (joueur.numLicense === selectedLicense) {
        option.selected = true;
      }
      selectElement.appendChild(option);
    });
  } catch (error) {
    console.error("Erreur lors du chargement des joueurs :", error);
    alert("Erreur lors du chargement des joueurs.");
  }
}

/**
 * Soumission du formulaire : on modifie le match (PATCH)
 * et on met à jour les participations de manière REST.
 */
async function modifierMatch(event) {
  event.preventDefault();

  try {
    const token = localStorage.getItem("token");
    if (!token) {
      window.location.href = "../Vue/Login.html";
      return false;
    }

    // Récupère les paramètres d'identification du match
    const urlParams = new URLSearchParams(window.location.search);
    const dateMatch = urlParams.get("dateMatch");
    const heure = urlParams.get("heure");

    if (!dateMatch || !heure) {
      throw new Error("Paramètres dateMatch et heure manquants dans l'URL");
    }

    // 1) Mettre à jour le match (PATCH)
    const matchData = {
      dateMatch: dateMatch, // inaltéré
      heure: heure, // inaltéré
      nomEquipeAdverse: document
        .getElementById("nomEquipeAdverse")
        .value.trim(),
      LieuRencontre: document.getElementById("lieuRencontre").value,
      scoreEquipeDomicile:
        document.getElementById("scoreEquipeDomicile").value || null,
      scoreEquipeExterne:
        document.getElementById("scoreEquipeExterne").value || null,
    };

    console.log("Sending match update (PATCH):", matchData);

    const response = await fetch(
      `https://drafteamapi.lespi.fr/Match/index.php?dateMatch=${encodeURIComponent(
        dateMatch
      )}&heure=${encodeURIComponent(heure)}`,
      {
        method: "PATCH",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(matchData),
      }
    );

    if (!response.ok) {
      if (response.status === 401) {
        logout();
      }
      const errorData = await response.json();
      throw new Error(
        errorData.status_message ||
          `Erreur HTTP lors de la modification du match (code ${response.status})`
      );
    }

    // 2) Récupérer les participations du formulaire
    const joueursSelects = document.querySelectorAll(
      'select[name="joueurs[]"]'
    );
    const statutsSelects = document.querySelectorAll(
      'select[name="statuts[]"]'
    );

    // Tableau des nouvelles participations côté front
    const newParticipations = [];
    for (let i = 0; i < joueursSelects.length; i++) {
      const numLicense = joueursSelects[i].value;
      if (!numLicense) continue; // Ignore les sélections vides

      newParticipations.push({
        numLicense: numLicense,
        estTitulaire: statutsSelects[i].value === "Titulaire" ? 1 : 0,
      });
    }

    // 3) Préparer une map pour les participations existantes
    //    Clé = numLicense, Valeur = { estTitulaire, ... }
    const existingMap = new Map();
    existingParticipations.forEach((p) => {
      existingMap.set(p.numLicense, p);
      // p contient { numLicense, dateMatch, heure, estTitulaire, ... }
    });

    // 4) Pour chaque participation du formulaire, faire un POST si nouvelle
    //    ou un PATCH si déjà existante mais différente
    for (const newPart of newParticipations) {
      const oldPart = existingMap.get(newPart.numLicense);

      // Cas a) la participation n'existe pas en base -> POST
      if (!oldPart) {
        const participationData = {
          numLicense: newPart.numLicense,
          dateMatch: dateMatch,
          heure: heure,
          estTitulaire: newPart.estTitulaire,
        };
        console.log("Creating participation (POST):", participationData);

        const partResponse = await fetch(
          "https://drafteamapi.lespi.fr/Participation/index.php",
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              Authorization: `Bearer ${token}`,
            },
            body: JSON.stringify(participationData),
          }
        );

        if (!partResponse.ok) {
          if (response.status === 401) {
            logout();
          }
          const errorData = await partResponse.json();
          console.error("Error adding participation:", errorData);
          throw new Error(
            errorData.status_message ||
              `Erreur lors de l'ajout de la participation`
          );
        }
      }
      // Cas b) la participation existe déjà -> vérifier si le statut a changé
      else {
        if (oldPart.estTitulaire !== newPart.estTitulaire) {
          // On fait un PATCH
          const patchData = {
            estTitulaire: newPart.estTitulaire,
          };
          console.log(
            "Patching participation (PATCH) numLicense=",
            newPart.numLicense,
            patchData
          );

          const patchResp = await fetch(
            `https://drafteamapi.lespi.fr/Participation/index.php?numLicense=${encodeURIComponent(
              newPart.numLicense
            )}&dateMatch=${encodeURIComponent(
              dateMatch
            )}&heure=${encodeURIComponent(heure)}`,
            {
              method: "PATCH",
              headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${token}`,
              },
              body: JSON.stringify(patchData),
            }
          );

          if (!patchResp.ok) {
            if (response.status === 401) {
              logout();
            }
            const errorData = await patchResp.json();
            console.error("Error patching participation:", errorData);
            throw new Error(
              errorData.status_message ||
                `Erreur lors de la modification de la participation`
            );
          }
        }
      }

      // Dans tous les cas, on retire la clé de la map pour marquer qu'elle est "traitée"
      existingMap.delete(newPart.numLicense);
    }

    // 5) Toute participation restant dans existingMap => supprimée côté front => DELETE
    for (const [numLicense, oldPart] of existingMap.entries()) {
      console.log("Deleting participation (DELETE) numLicense=", numLicense);

      const deleteResp = await fetch(
        `https://drafteamapi.lespi.fr/Participation/index.php?numLicense=${encodeURIComponent(
          numLicense
        )}&dateMatch=${encodeURIComponent(
          dateMatch
        )}&heure=${encodeURIComponent(heure)}`,
        {
          method: "DELETE",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (!deleteResp.ok) {
        if (response.status === 401) {
          logout();
        }
        const errorData = await deleteResp.json();
        console.error("Error deleting participation:", errorData);
        // Selon votre logique, vous pouvez lever une erreur ou simplement continuer
        throw new Error(
          errorData.status_message ||
            `Erreur lors de la suppression de la participation`
        );
      }
    }

    // 6) Si tout s'est bien passé, on redirige vers la page de gestion
    window.location.href = "../Vue/GestionMatchs.html";
    return false;
  } catch (error) {
    console.error(
      "Erreur lors de la modification du match ou des participations :",
      error
    );
    alert(
      error.message || "Erreur lors de la modification du match/participations."
    );
    return false;
  }
}
