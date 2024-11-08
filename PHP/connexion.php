<?php

class Database {
    private static $instance = null; // Instance unique de la classe
    private $pdo; // Objet PDO pour la connexion

    // Constructeur privé pour empêcher l'instanciation directe
    private function __construct($username, $password) {
        try {
            $this->pdo = new PDO("mysql:host=localhost;dbname=u847486544_drafteam", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    // Méthode pour obtenir l'instance unique de la classe
    public static function getInstance($username = null, $password = null) {
        if (self::$instance === null) {
            if ($username === null || $password === null) {
                throw new Exception("Username et password requis pour la première connexion.");
            }
            self::$instance = new self($username, $password);
        }
        return self::$instance;
    }

    // Méthode pour obtenir l'objet PDO
    public function getConnection() {
        return $this->pdo;
    }

    // Empêche la duplication de l'instance
    private function __clone() {}
    private function __wakeup() {}
}

// Exemple d'utilisation (première connexion avec username et password)
try {
    $db = Database::getInstance("root", 'Jesaplgrout123456789*')->getConnection();
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
