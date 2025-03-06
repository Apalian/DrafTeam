document.addEventListener("DOMContentLoaded", () => {
    loadMatchs();
  });
  
  // Fonction pour charger les matchs via l'API
  async function loadMatchs(searchQuery = "") {
    try {
      const token = localStorage.getItem("token");
      if (!token) {
        window.location.href = "../Vue/Login.html";
        return;
      }
  
      // Construction de l'URL de l'API pour les matchs
      let url = `https://drafteamapi.lespi.fr/Match/index.php`;
  
      const response = await fetch(url, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`
        }
      });
  
      if (!response.ok) {
        throw new Error(`Erreur HTTP! statut: ${response.status}`);
      }
  
      const matchs = await response.json();
      displayMatchs(matchs);
    } catch (error) {
      console.error("Erreur lors du chargement des matchs:", error);
      document.getElementById("matchsList").innerHTML = "<p>Erreur lors du chargement des matchs.</p>";
    }
  }
  
  // Fonction pour afficher les matchs
  function displayMatchs(matchs) {
    const matchsList = document.getElementById("matchsList");
    const template = document.getElementById("matchTemplate");
  
    if (!matchs || matchs.length === 0) {
      matchsList.innerHTML = "<p>Aucun match trouvé.</p>";
      return;
    }
  
    matchsList.innerHTML = "";
  
    matchs.forEach(match => {
      const clone = template.content.cloneNode(true);
  
      // Remplir les informations du match
      clone.querySelector(".date-match").textContent = match.dateMatch;
      clone.querySelector(".heure-match").textContent = match.heure;
      clone.querySelector(".nom-equipe").textContent = match.nomEquipeAdverse;
      clone.querySelector(".lieu-rencontre").textContent = match.lieuRencontre;
  
      // Gestion des scores
      const scoreDomicile = clone.querySelector(".score-domicile");
      const scoreExterne = clone.querySelector(".score-externe");
  
      scoreDomicile.textContent = match.scoreEquipeDomicile === null ? "pas de score" : match.scoreEquipeDomicile;
      scoreExterne.textContent = match.scoreEquipeExterne === null ? "pas de score" : match.scoreEquipeExterne;
  
      scoreDomicile.className = `score-domicile ${getScoreClass(match.scoreEquipeDomicile, match.scoreEquipeExterne, true)}`;
      scoreExterne.className = `score-externe ${getScoreClass(match.scoreEquipeDomicile, match.scoreEquipeExterne, false)}`;
  
      // Gestion du bouton de suppression
      const btnSupprimer = clone.querySelector(".btn-supprimer");
      if (isMatchPassed(match.dateMatch)) {
        btnSupprimer.disabled = true;
        btnSupprimer.title = "Ce match est déjà passé et ne peut pas être supprimé.";
      }
      btnSupprimer.dataset.dateMatch = match.dateMatch;
      btnSupprimer.dataset.heure = match.heure;
  
      // Stocker les données dans le bouton de modification
      const btnModifier = clone.querySelector(".btn-modifier");
      btnModifier.dataset.dateMatch = match.dateMatch;
      btnModifier.dataset.heure = match.heure;
  
      matchsList.appendChild(clone);
    });
  }
  
  // Fonction pour déterminer la classe CSS en fonction des scores
  function getScoreClass(scoreDomicile, scoreExterne, isDomicile) {
    if (scoreDomicile === null || scoreExterne === null) {
      return "score-unknown";
    }
    if (scoreDomicile === scoreExterne) {
      return "score-gray";
    }
    return isDomicile
      ? (scoreDomicile > scoreExterne ? "score-green" : "score-red")
      : (scoreExterne > scoreDomicile ? "score-green" : "score-red");
  }
  
  // Vérifier si un match est déjà passé
  function isMatchPassed(dateMatch) {
    const matchDate = new Date(dateMatch);
    const today = new Date();
    return matchDate < today;
  }
  
  // Fonction pour supprimer un match via l'API
  async function supprimerMatch(button) {
    const dateMatch = button.dataset.dateMatch;
    const heure = button.dataset.heure;
  
    if (!confirm("Êtes-vous sûr de vouloir supprimer ce match ?")) {
      return;
    }
  
    try {
      const token = localStorage.getItem("token");
      if (!token) {
        window.location.href = "../Vue/Login.html";
        return;
      }
  
      const response = await fetch("https://drafteamapi.lespi.fr/Match/index.php", {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`
        },
        body: JSON.stringify({
          dateMatch,
          heure
        })
      });
  
      if (!response.ok) {
        throw new Error(`Erreur HTTP! statut: ${response.status}`);
      }
  
      // Recharger la liste des matchs après suppression
      loadMatchs(document.getElementById("searchInput").value);
    } catch (error) {
      console.error("Erreur lors de la suppression du match:", error);
      alert("Erreur lors de la suppression du match.");
    }
  }
  
  // Fonction pour modifier un match (redirection vers la page de modification)
  function modifierMatch(button) {
    const dateMatch = button.dataset.dateMatch;
    const heure = button.dataset.heure;
    window.location.href = `../Vue/ModifierMatch.html?dateMatch=${encodeURIComponent(dateMatch)}&heure=${encodeURIComponent(heure)}`;
  }
  
  // Fonction pour lancer la recherche des matchs
  function searchMatches() {
    const searchQuery = document.getElementById("searchInput").value;
    loadMatchs(searchQuery);
  }
  