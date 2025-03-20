// Configuration de l'API
const API_CONFIG = {
  BASE_URL: "https://drafteamapi.lespi.fr",
  AUTH_URL: "https://drafteamauthentication.lespi.fr",
};

// Fonction pour vérifier l'authentification
function checkAuth() {
  const token = localStorage.getItem("token");
  if (!token) {
    window.location.href = "../Vue/Login.html";
    return false;
  }
  return token;
}

// Fonction pour gérer les erreurs d'authentification
function handleAuthError(error) {
  if (error.status === 401) {
    localStorage.removeItem("token");
    window.location.href = "../Vue/Login.html";
  }
  throw error;
}

// Fonction pour faire une requête API authentifiée
async function fetchWithAuth(url, options = {}) {
  const token = checkAuth();

  const defaultOptions = {
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
  };

  const response = await fetch(url, { ...defaultOptions, ...options });

  if (!response.ok) {
    handleAuthError({ status: response.status });
  }

  return response;
}

// Fonction de déconnexion
function logout() {
  localStorage.clear();
  window.location.href = "./Vue/Login.html";
}

// Fonction pour vérifier si une date est passée
function isDatePassed(date) {
  const givenDate = new Date(date);
  const today = new Date();
  return givenDate < today;
}

// Export des fonctions et configurations
export {
  API_CONFIG,
  checkAuth,
  handleAuthError,
  fetchWithAuth,
  logout,
  isDatePassed,
};
