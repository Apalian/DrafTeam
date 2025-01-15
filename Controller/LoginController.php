<?php
// Affichage des erreurs sur Hostinger
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    header("Location: ../index.php");
    exit();
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Vérifiez les informations d'identification avec la base de données (exemple simple)
    $user = "admin";
    $pass = "admin";

    if ($username === $user && $password === $pass) {
        $_SESSION['username'] = "u847486544_root";
        $_SESSION['password'] = "Jesaplgrout123456789*";

        header("Location: ../index.php");
        exit();
    } else {
        // Rediriger avec un message d'erreur
        header("Location: ../Vue/Login.php?error=Nom d'utilisateur ou mot de passe incorrect");
        exit();
    }
}

