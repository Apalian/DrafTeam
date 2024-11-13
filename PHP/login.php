<?php
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
            $_SESSION['db_instance'] = Connexion::getInstance("u847486544_root", "Jesaplgrout123456789*");
            echo "Connexion réussie";
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