<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$mot_de_passe = '$iutinfo';
$mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_BCRYPT);
echo $mot_de_passe_hache;
?>
