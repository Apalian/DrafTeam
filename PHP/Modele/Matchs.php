<?php

class Matchs
{
    private $dateMatch;
    private $heure;
    private $nomEquipeAdverse;
    private $lieuRencontre;
    private $ScoreEquipeDomicile;
    private $scoreEquipeExterne;


    public function __construct($dateMatch,$heure,$nomEquipeAdverse,$lieuRencontre,$ScoreEquipeDomicile,$scoreEquipeExterne){
        $this->dateMatch = $dateMatch;
        $this->heure = $heure;
        $this->nomEquipeAdverse = $nomEquipeAdverse;
        $this->lieuRencontre = $lieuRencontre;
        $this->ScoreEquipeDomicile = $ScoreEquipeDomicile;
        $this->scoreEquipeExterne = $scoreEquipeExterne;
    }
}