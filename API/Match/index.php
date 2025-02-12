<?php
require_once 'connexionDB.php';
require_once 'functions.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
$dateMatch = isset($_GET['dateMatch']) ? $_GET['dateMatch'] : null;
$heure = isset($_GET['heure']) ? $_GET['heure'] : null;
// Vérifie la méthode de requête
switch ($_SERVER['REQUEST_METHOD']){
    case 'GET' :
            echo readMatch($linkpdo, $dateMatch, $heure);
            break;
    case 'POST' :
        $input = json_decode(file_get_contents("php://input"), true);
            $dateMatch = $input['dateMatch'];
            $heure = isset($input['heure']) ? $input['heure'] : null;
            $nomEquipeAdverse = isset($input['nomEquipeAdverse']) ? $input['nomEquipeAdverse'] : null;
            $LieuRencontre = isset($input['LieuRencontre']) ? $input['LieuRencontre'] : null;
            $scoreEquipeDomicile = isset($input['scoreEquipeDomicile']) ? $input['scoreEquipeDomicile'] : null;
            $scoreEquipeExterne = isset($input['scoreEquipeExterne']) ? $input['scoreEquipeExterne'] : null;

            echo writeMatch($linkpdo, $dateMatch, $heure, $nomEquipeAdverse, $LieuRencontre, $scoreEquipeDomicile, $scoreEquipeExterne);
        break;
    case 'PATCH' :
        $input = json_decode(file_get_contents("php://input"), true);
        echo patchMatch($linkpdo, $dateMatch, isset($input['heure']) ? $input['heure'] : null, isset($input['nomEquipeAdverse']) ? $input['nomEquipeAdverse'] : null, isset($input['LieuRencontre']) ? $input['LieuRencontre'] : null, isset($input['scoreEquipeDomicile']) ? $input['scoreEquipeDomicile'] : null, isset($input['scoreEquipeExterne']) ? $input['scoreEquipeExterne'] : null);
        break;

    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);
        echo putMatch($linkpdo, $dateMatch, $heure, $nomEquipeAdverse, $LieuRencontre, $scoreEquipeDomicile, $scoreEquipeExterne);
        break;

    case 'DELETE':
        echo deleteMatch($linkpdo, $dateMatch, $heure);
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
