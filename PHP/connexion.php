<?php
class Connexion {
    private static $instance = null;
    private $pdo;

    private function __construct($username, $password) {
        try {
            $this->pdo = new PDO("mysql:host=localhost;dbname=u847486544_drafteam", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    public static function getInstance($username = null, $password = null) {
        if (self::$instance === null) {
            if ($username === null || $password === null) {
                throw new Exception("Username et password requis pour la première connexion.");
            }
            self::$instance = new self($username, $password);
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    private function __clone() {}
    private function __wakeup() {}
}
