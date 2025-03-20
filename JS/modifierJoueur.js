import { API_CONFIG, fetchWithAuth } from "./common.js";

document.addEventListener("DOMContentLoaded", async () => {
  const form = document.querySelector(".player-form");
  const numLicense = new URLSearchParams(window.location.search).get(
    "numLicense"
  );

  let initialData = {}; // Pour stocker les données initiales du joueur

  // Récupérer les données du joueur
  try {
    const response = await fetchWithAuth(
      `${API_CONFIG.BASE_URL}/Joueur/?numLicense=${numLicense}`
    );
    const jsonData = await response.json();
    initialData = jsonData.data[0]; // Stocker les données initiales
    // Remplir les champs du formulaire avec les données du joueur
    document.getElementById("nom").value = initialData.nom;
    document.getElementById("prenom").value = initialData.prenom;
    document.getElementById("dateNaissance").value = initialData.dateNaissance;

    // Pré-sélectionner le statut
    const statutSelect = document.getElementById("statut");
    statutSelect.value = initialData.statuts; // Pré-sélectionner le statut

    document.getElementById("commentaire").value = initialData.commentaire;
    document.getElementById("taille").value = initialData.taille;
    document.getElementById("poids").value = initialData.poids;
  } catch (error) {
    console.error(
      "Erreur lors de la récupération des données du joueur:",
      error
    );
  }

  form.addEventListener("submit", async (event) => {
    event.preventDefault(); // Empêche le rechargement de la page

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    // Vérifier si les données ont changé
    const hasChanged = Object.keys(initialData).some(
      (key) => initialData[key] !== data[key]
    );

    if (!hasChanged) {
      alert("Aucune modification détectée.");
      return; // Ne pas envoyer de requête si aucune modification
    }

    try {
      const method = Object.values(data).every((value) => value !== "")
        ? "PUT"
        : "PATCH"; // Choisir la méthode

      const response = await fetchWithAuth(
        `${API_CONFIG.BASE_URL}/Joueur/?numLicense=${numLicense}`,
        {
          method: method,
          body: JSON.stringify(data),
        }
      );

      if (!response.ok) {
        throw new Error(`Erreur HTTP: ${response.status}`);
      }

      alert("Joueur modifié avec succès !");
      window.location.href = "./GestionJoueurs.html";
    } catch (error) {
      console.error("Erreur lors de la modification du joueur:", error);
    }
  });
});
