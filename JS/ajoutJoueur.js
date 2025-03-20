async function ajouterJoueur(event) {
  event.preventDefault(); // Empêche le rechargement de la page

  const token = localStorage.getItem("token");
  const numLicense = document.getElementById("numLicense").value;
  const joueurData = {
    nom: document.getElementById("nom").value,
    prenom: document.getElementById("prenom").value,
    dateNaissance: document.getElementById("dateNaissance").value,
    commentaire: document.getElementById("commentaire").value,
    statut: document.getElementById("statut").value,
    taille: document.getElementById("taille").value,
    poids: document.getElementById("poids").value,
  };
  try {
    const response = await fetchWithAuth(
      `https://drafteamapi.lespi.fr/Joueur/index.php?numLicense=${numLicense}`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(joueurData),
      }
    );
    if (!response.ok) {
      if (response.status === 401) {
        logout();
      }
      throw new Error("Erreur lors de l'ajout du joueur");
    }
    // Rediriger ou afficher un message de succès
    window.location.href = "./GestionJoueurs.html";
    return false; // Empêche la soumission par défaut
  } catch (error) {
    console.error("Erreur:", error);
    return false; // Empêche la soumission en cas d'erreur
  }
}
