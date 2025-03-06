// Fonction pour ajouter un joueur
async function ajouterJoueur() {
  const token = localStorage.getItem("token");
  const joueurData = {
    numLicense: document.getElementById("numLicense").value,
    nom: document.getElementById("nom").value,
    prenom: document.getElementById("prenom").value,
    dateNaissance: document.getElementById("dateNaissance").value,
    commentaire: document.getElementById("commentaire").value,
    statut: document.getElementById("statut").value,
    taille: document.getElementById("taille").value,
    poids: document.getElementById("poids").value,
  };

  try {
    const response = await fetch("drafteamapi/lespi.fr/Joueur/index.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify(joueurData),
    });

    if (!response.ok) {
      throw new Error("Erreur lors de l'ajout du joueur");
    }

    // Rediriger ou afficher un message de succès
    window.location.href = "GestionJoueursController.php";
    return false; // Empêche la soumission par défaut
  } catch (error) {
    console.error("Erreur:", error);
    return false; // Empêche la soumission en cas d'erreur
  }
}
