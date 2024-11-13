<?php

session_start(); // Démarre la session

// 1. Supprime toutes les variables de session
$_SESSION = [];

// 2. Détruit la session
session_destroy();

// 3. Optionnel : Supprime également le cookie de session s’il existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirige l'utilisateur vers la page de connexion (ou autre)
header("Location: ../index.php");
exit();
?>