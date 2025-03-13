document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector(".player-form");
  const numLicense = new URLSearchParams(window.location.search).get(
    "numLicense"
  );

  form.addEventListener("submit", async (event) => {
    event.preventDefault(); // Empêche le rechargement de la page

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    // Vérifier si tous les champs sont remplis
    const allFieldsFilled = Object.values(data).every((value) => value !== "");

    try {
      const token = localStorage.getItem("token");
      if (!token) {
        window.location.href = "../Vue/Login.html";
        return;
      }

      const method = allFieldsFilled ? "PUT" : "PATCH"; // Choisir la méthode

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
