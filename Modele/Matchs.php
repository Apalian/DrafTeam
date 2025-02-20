<?php

namespace Modele;

class Matchs
{
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
    private $nomEquipeAdverse;
    /**
     * @var
     */
    private $lieuRencontre;
    /**
     * @var
     */
    private $ScoreEquipeDomicile;
    /**
     * @var
     */
    private $scoreEquipeExterne;


    /**
     * @param $dateMatch
     * @param $heure
     * @param $nomEquipeAdverse
     * @param $lieuRencontre
     * @param $ScoreEquipeDomicile
     * @param $scoreEquipeExterne
     */
    public function __construct($dateMatch, $heure, $nomEquipeAdverse, $lieuRencontre, $ScoreEquipeDomicile, $scoreEquipeExterne)
    {
        $this->dateMatch = $dateMatch;
        $this->heure = $heure;
        $this->nomEquipeAdverse = $nomEquipeAdverse;
        $this->lieuRencontre = $lieuRencontre;
        $this->ScoreEquipeDomicile = $ScoreEquipeDomicile;
        $this->scoreEquipeExterne = $scoreEquipeExterne;
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
    public function getNomEquipeAdverse()
    {
        return $this->nomEquipeAdverse;
    }

    /**
     * @param mixed $nomEquipeAdverse
     */
    public function setNomEquipeAdverse($nomEquipeAdverse): void
    {
        $this->nomEquipeAdverse = $nomEquipeAdverse;
    }

    /**
     * @return mixed
     */
    public function getLieuRencontre()
    {
        return $this->lieuRencontre;
    }

    /**
     * @param mixed $lieuRencontre
     */
    public function setLieuRencontre($lieuRencontre): void
    {
        $this->lieuRencontre = $lieuRencontre;
    }

    /**
     * @return mixed
     */
    public function getScoreEquipeDomicile()
    {
        return $this->ScoreEquipeDomicile;
    }

    /**
     * @param mixed $ScoreEquipeDomicile
     */
    public function setScoreEquipeDomicile($ScoreEquipeDomicile): void
    {
        $this->ScoreEquipeDomicile = $ScoreEquipeDomicile;
    }

    /**
     * @return mixed
     */
    public function getScoreEquipeExterne()
    {
        return $this->scoreEquipeExterne;
    }

    /**
     * @param mixed $scoreEquipeExterne
     */
    public function setScoreEquipeExterne($scoreEquipeExterne): void
    {
        $this->scoreEquipeExterne = $scoreEquipeExterne;
    }

    public function isMatchPassed() {
        $matchDateTime = new \DateTime($this->getDateMatch() . ' ' . $this->getHeure());
        $currentDateTime = new \DateTime();
        return $matchDateTime < $currentDateTime;
    }

}