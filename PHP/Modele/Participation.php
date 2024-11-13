<?php

class Participation
{
    private $numLicense;
    private $dateMatch;
    private $heure;
    private $estTitulaire;
    private $evaluation;
    private $poste;

    public function __construct($numLicense,$dateMatch,$heure,$estTitulaire,$evaluation,$poste){
        $this->numLicense = $numLicense;
        $this->dateMatch = $dateMatch;
        $this->heure = $heure;
        $this->estTitulaire = $estTitulaire;
        $this->evaluation = $evaluation;
        $this->poste = $poste;
    }
}