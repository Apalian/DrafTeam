<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'connexionDB.php';
require_once 'functions.php';


header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
$numLicense = isset($_GET['numLicense']) ? intval($_GET['numLicense']) : null;
// Vérifie la méthode de requête
switch ($_SERVER['REQUEST_METHOD']){
    case 'GET' :
            echo readJoueur($linkpdo, $numLicense);
            break;
    case 'POST' :
        $input = json_decode(file_get_contents("php://input"), true);
            $nom = $input['nom'];
            $prenom = isset($input['prenom']) ? $input['prenom'] : null;
            $dateNaissance = isset($input['dateNaissance']) ? $input['dateNaissance'] : null;
            $commentaire = isset($input['commentaire']) ? $input['commentaire'] : null;
            $statut = isset($input['statut']) ? $input['statut'] : null;
            $taille = isset($input['taille']) ? $input['taille'] : null;
            $poids = isset($input['poids']) ? $input['poids'] : null;

            echo writeJoueur($linkpdo, $nom, $prenom, $dateNaissance, $commentaire, $statut, $taille, $poids);
        break;
    case 'PATCH' :
        $input = json_decode(file_get_contents("php://input"), true);
        echo patchJoueur($linkpdo, $numLicense, isset($input['nom']) ? $input['nom'] : null, isset($input['prenom']) ? $input['prenom'] : null, isset($input['dateNaissance']) ? $input['dateNaissance'] : null, isset($input['commentaire']) ? $input['commentaire'] : null, isset($input['statut']) ? $input['statut'] : null, isset($input['taille']) ? $input['taille'] : null, isset($input['poids']) ? $input['poids'] : null);
        break;

    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);
        echo putJoueur($linkpdo, $numLicense, $input['nom'], $input['prenom'], $input['dateNaissance'], $input['commentaire'], $input['statut'], $input['taille'], $input['poids']);
        break;

    case 'DELETE':
        echo deleteJoueur($linkpdo, $numLicense);
        break;

    case 'OPTIONS':
        http_response_code(204);
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "status_code" => 405, "status_message" => "Méthode non autorisée"], JSON_PRETTY_PRINT);
        break;
}
