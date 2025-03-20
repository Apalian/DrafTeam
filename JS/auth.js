// Function to check authentication
function checkAuth() {
  const username = localStorage.getItem("username");
  const token = localStorage.getItem("token");

  if (!username || !token) {
    window.location.href = "../Vue/Login.php";
    return false;
  }
  return true;
}
