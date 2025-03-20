function logout() {
  localStorage.clear();
  window.location.href = "../Vue/Login.html";
}
// Fonction pour gérer les erreurs d'authentification
function handleAuthError(response) {
  if (response.status === 401) {
    localStorage.clear();
    window.location.href = "../Vue/Login.html";
  }
  return response;
}

// Fonction pour faire des requêtes fetch avec gestion automatique des erreurs 401
async function fetchWithAuth(url, options = {}) {
  const response = await fetch(url, options);
  return handleAuthError(response);
}
