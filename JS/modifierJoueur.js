document.addEventListener("DOMContentLoaded", async () => {
  const form = document.querySelector(".player-form");
  const numLicense = new URLSearchParams(window.location.search).get(
    "numLicense"
  );

  let initialData = {}; // Pour stocker les données initiales du joueur

  // Récupérer les données du joueur
  try {
    const token = localStorage.getItem("token");
    if (!token) {
      window.location.href = "../Vue/Login.html";
      return;
    }

    const response = await fetch(
      `https://drafteamapi.lespi.fr/Joueur/?numLicense=${numLicense}`,
      {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    );

    if (!response.ok) {
      console.error("Erreur HTTP:", response.status, response.statusText);
      return;
    }

    initialData = await response.json(); // Stocker les données initiales
    console.log(initialData);
    // Remplir les champs du formulaire avec les données du joueur
    document.getElementById("nom").value = initialData.data.nom;
    document.getElementById("prenom").value = initialData.data.prenom;
    document.getElementById("dateNaissance").value =
      initialData.data.dateNaissance;
    document.getElementById("statut").value = initialData.data.statuts;
    document.getElementById("commentaire").value = initialData.data.commentaire;
    document.getElementById("taille").value = initialData.data.taille;
    document.getElementById("poids").value = initialData.data.poids;
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
      const token = localStorage.getItem("token");
      if (!token) {
        window.location.href = "../Vue/Login.html";
        return;
      }

      const method = Object.values(data).every((value) => value !== "")
        ? "PUT"
        : "PATCH"; // Choisir la méthode

      const response = await fetch(
        `https://drafteamapi.lespi.fr/Joueur/?numLicense=${numLicense}`,
        {
          method: method,
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data),
        }
      );

      if (!response.ok) {
        console.error("Erreur HTTP:", response.status, response.statusText);
        return;
      }

      alert("Joueur modifié avec succès !");
      window.location.href = "../GestionJoueurs.html";
    } catch (error) {
      console.error("Erreur lors de la modification du joueur:", error);
    }
  });
});
