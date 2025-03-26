let existingParticipations = [];

// Store original match details
let originalDateMatch = null;
let originalHeure = null;

document.addEventListener("DOMContentLoaded", () => {
  const urlParams = new URLSearchParams(window.location.search);
  const dateMatch = urlParams.get("dateMatch");
  const heure = urlParams.get("heure");

  console.log("URL Parameters:", { dateMatch, heure });

  if (dateMatch && heure) {
    // Store original values
    originalDateMatch = dateMatch;
    originalHeure = heure;
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
    const response = await fetchWithAuth(
      `https://drafteamapi.lespi.fr/Match/index.php?dateMatch=${encodeURIComponent(
        dateMatch
      )}&heure=${encodeURIComponent(heure)}`,
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );

    if (!response.ok) {
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
  
      const url = new URL("https://drafteamapi.lespi.fr/Participation/index.php");
      url.searchParams.append("dateMatch", dateMatch);
      url.searchParams.append("heure", heure);
  
      const response = await fetchWithAuth(url, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
  
      const container = document.getElementById("participations-container");
      container.innerHTML = "";
  
      if (response.status === 404) {
        container.innerHTML = "<p>Aucune participation trouvée pour ce match.</p>";
        existingParticipations = [];
        return; // Ne pas lancer d'erreur, gérer simplement ce cas
      }
  
      if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`Erreur HTTP! statut: ${response.status}, détail: ${errorText}`);
      }
  
      const data = await response.json();
  
      existingParticipations = Array.isArray(data.data) ? data.data : [];
  
      if (existingParticipations.length > 0) {
        existingParticipations.forEach((participation) => {
          ajouterParticipation(participation);
        });
      } else {
        container.innerHTML = "<p>Aucune participation trouvée pour ce match.</p>";
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
    const response = await fetchWithAuth(
      "https://drafteamapi.lespi.fr/Joueur/index.php",
      {
        headers: { Authorization: `Bearer ${token}` },
      }
    );

    if (!response.ok) {
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
      const token = localStorage.getItem('token');
      if (!token) {
        alert('Token manquant, veuillez vous reconnecter.');
        return;
      }
  
      if (!originalDateMatch || !originalHeure) {
        throw new Error('Paramètres dateMatch et heure manquants');
      }
  
      const currentDateMatch = document.getElementById('dateMatch').value;
      const currentHeure = document.getElementById('heure').value;
  
      const matchData = {
        dateMatch: currentDateMatch,
        heure: currentHeure,
        nomEquipeAdverse: document.getElementById('nomEquipeAdverse').value.trim(),
        lieuRencontre: document.getElementById('lieuRencontre').value,
        scoreEquipeDomicile: document.getElementById('scoreEquipeDomicile').value || null,
        scoreEquipeExterne: document.getElementById('scoreEquipeExterne').value || null
      };
  
      // Mise à jour du match
      const matchResponse = await fetch(`https://drafteamapi.lespi.fr/Match/index.php?dateMatch=${encodeURIComponent(originalDateMatch)}&heure=${encodeURIComponent(originalHeure)}`, {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(matchData)
      });
  
      if (!matchResponse.ok) {
        throw new Error('Erreur lors de la mise à jour du match');
      }
  
      // Récupération des sélections de joueurs et statuts
      const joueursSelects = document.querySelectorAll('select[name="joueurs[]"]');
      const statutsSelects = document.querySelectorAll('select[name="statuts[]"]');
  
      const updatedParticipations = [];
  
      for (let i = 0; i < joueursSelects.length; i++) {
        const numLicense = joueursSelects[i].value;
        const statut = statutsSelects[i].value;
  
        if (!numLicense) {
          alert("Veuillez sélectionner un joueur pour chaque participation.");
          return;
        }
  
        updatedParticipations.push({
          numLicense,
          estTitulaire: statut === 'Titulaire' ? 1 : 0
        });
      }
  
      const participationsToDelete = existingParticipations.filter(ep => !updatedParticipations.some(up => up.numLicense === ep.numLicense));
      const participationsToAdd = updatedParticipations.filter(up => !existingParticipations.some(ep => ep.numLicense === up.numLicense));
      const participationsToUpdate = updatedParticipations.filter(up => existingParticipations.some(ep => ep.numLicense === up.numLicense && ep.estTitulaire !== up.estTitulaire));
  
      // Suppression des participations retirées
      for (const participation of participationsToDelete) {
        await fetch(`https://drafteamapi.lespi.fr/Participation/index.php?numLicense=${encodeURIComponent(participation.numLicense)}&dateMatch=${encodeURIComponent(originalDateMatch)}&heure=${encodeURIComponent(originalHeure)}`, {
          method: 'DELETE',
          headers: {'Authorization': `Bearer ${token}`}
        });
      }
  
      // Ajout des nouvelles participations
      // Ajout des nouvelles participations
for (const participation of participationsToAdd) {
    const participationBody = {
      numLicense: participation.numLicense,
      dateMatch: currentDateMatch,
      heure: currentHeure,
      estTitulaire: participation.estTitulaire,
      endurance: 0,
      vitesse: 0,
      defense: 0,
      tirs: 0,
      passes: 0,
      poste: null
    };
  
    const response = await fetch('https://drafteamapi.lespi.fr/Participation/index.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify(participationBody)
    });
  
    if (!response.ok) {
      const errorDetails = await response.json();
      console.error('Détails erreur API Participation:', errorDetails);
      alert(`Erreur API: ${errorDetails.status_message}`);
    }
  }
  
  
      // Modification des participations existantes changées
      for (const participation of participationsToUpdate) {
        await fetch(`https://drafteamapi.lespi.fr/Participation/index.php?numLicense=${encodeURIComponent(participation.numLicense)}&dateMatch=${encodeURIComponent(originalDateMatch)}&heure=${encodeURIComponent(originalHeure)}`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({estTitulaire: participation.estTitulaire})
        });
      }
  
      console.log('Modification terminée avec succès.');
    } catch (error) {
      console.error('Erreur lors de la modification :', error);
      alert(error.message);
    }
  }