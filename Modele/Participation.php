<?php

namespace Modele;

class Participation
{
    private $numLicense;
    private $dateMatch;
    private $heure;
    private $estTitulaire;
    private $endurance;
    private $vitesse;
    private $defense;
    private $tirs;
    private $passes;
    private $poste;

    public function __construct($numLicense, $dateMatch, $heure, $estTitulaire, $endurance, $vitesse, $defense, $tirs, $passes, $poste)
    {
        $this->numLicense = $numLicense;
        $this->dateMatch = $dateMatch;
        $this->heure = $heure;
        $this->estTitulaire = $estTitulaire;
        $this->endurance = $endurance;
        $this->vitesse = $vitesse;
        $this->defense = $defense;
        $this->tirs = $tirs;
        $this->passes = $passes;
        $this->poste = $poste;
    }

    public function getNumLicense() { return $this->numLicense; }
    public function setNumLicense($numLicense): void { $this->numLicense = $numLicense; }

    public function getDateMatch() { return $this->dateMatch; }
    public function setDateMatch($dateMatch): void { $this->dateMatch = $dateMatch; }

    public function getHeure() { return $this->heure; }
    public function setHeure($heure): void { $this->heure = $heure; }

    public function getEstTitulaire() { return $this->estTitulaire; }
    public function setEstTitulaire($estTitulaire): void { $this->estTitulaire = $estTitulaire; }

    public function getEndurance() { return $this->endurance; }
    public function setEndurance($endurance): void { $this->endurance = $endurance; }

    public function getVitesse() { return $this->vitesse; }
    public function setVitesse($vitesse): void { $this->vitesse = $vitesse; }

    public function getDefense() { return $this->defense; }
    public function setDefense($defense): void { $this->defense = $defense; }

    public function getTirs() { return $this->tirs; }
    public function setTirs($tirs): void { $this->tirs = $tirs; }

    public function getPasses() { return $this->passes; }
    public function setPasses($passes): void { $this->passes = $passes; }

    public function getPoste() { return $this->poste; }
    public function setPoste($poste): void { $this->poste = $poste; }
}
