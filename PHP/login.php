<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();
require_once 'Connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Vérifiez les informations d'identification avec la base de données (exemple simple)
    $user = "admin";
    $pass = "admin";

    if ($username === $user && $password === $pass) {
        // Instancie l'objet Database uniquement après la vérification
        try {
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;
            echo "Connexion réussie";
            header("Location: ../index.php?error=" . urlencode($error));
            exit();
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        // Message d'erreur pour l'authentification échouée
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
        header("Location: ../index.php?error=" . urlencode($error));
        exit();
    }
}