<?php

namespace Modele;
class Participation
{
    /**
     * @var
     */
    private $numLicense;
    /**
     * @var
     */
    private $dateMatch;
    /**
     * @var
     */
    private $heure;
    /**
     * @var
     */
    private $estTitulaire;
    /**
     * @var
     */
    private $evaluation;
    /**
     * @var
     */
    private $poste;

    /**
     * @param $numLicense
     * @param $dateMatch
     * @param $heure
     * @param $estTitulaire
     * @param $evaluation
     * @param $poste
     */
    public function __construct($numLicense, $dateMatch, $heure, $estTitulaire, $evaluation, $poste)
    {
        $this->numLicense = $numLicense;
        $this->dateMatch = $dateMatch;
        $this->heure = $heure;
        $this->estTitulaire = $estTitulaire;
        $this->evaluation = $evaluation;
        $this->poste = $poste;
    }

    /**
     * @return mixed
     */
    public function getNumLicense()
    {
        return $this->numLicense;
    }

    /**
     * @param mixed $numLicense
     */
    public function setNumLicense($numLicense): void
    {
        $this->numLicense = $numLicense;
    }

    /**
     * @return mixed
     */
    public function getDateMatch()
    {
        return $this->dateMatch;
    }

    /**
     * @param mixed $dateMatch
     */
    public function setDateMatch($dateMatch): void
    {
        $this->dateMatch = $dateMatch;
    }

    /**
     * @return mixed
     */
    public function getHeure()
    {
        return $this->heure;
    }

    /**
     * @param mixed $heure
     */
    public function setHeure($heure): void
    {
        $this->heure = $heure;
    }

    /**
     * @return mixed
     */
    public function getEstTitulaire()
    {
        return $this->estTitulaire;
    }

    /**
     * @param mixed $estTitulaire
     */
    public function setEstTitulaire($estTitulaire): void
    {
        $this->estTitulaire = $estTitulaire;
    }

    /**
     * @return mixed
     */
    public function getEvaluation()
    {
        return $this->evaluation;
    }

    /**
     * @param mixed $evaluation
     */
    public function setEvaluation($evaluation): void
    {
        $this->evaluation = $evaluation;
    }

    /**
     * @return mixed
     */
    public function getPoste()
    {
        return $this->poste;
    }

    /**
     * @param mixed $poste
     */
    public function setPoste($poste): void
    {
        $this->poste = $poste;
    }

}