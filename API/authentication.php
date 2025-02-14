<?php
$mot_de_passe = '$iutinfo';
$mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_BCRYPT);
echo $mot_de_passe_hache;
?>
