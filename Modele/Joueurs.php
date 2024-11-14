<?php

namespace Modele;
class Joueurs
{
    /**
     * @var
     */
    private $numLicense;
    /**
     * @var
     */
    private $nom;
    /**
     * @var
     */
    private $prenom;
    /**
     * @var
     */
    private $dateNaissance;
    /**
     * @var
     */
    private $commentaire;
    /**
     * @var
     */
    private $statut;
    /**
     * @var
     */
    private $taille;
    /**
     * @var
     */
    private $poids;

    /**
     * @param $numLicense
     * @param $nom
     * @param $prenom
     * @param $dateNaissance
     * @param $commentaire
     * @param $statut
     * @param $taille
     * @param $poids
     */
    public function __construct($numLicense, $nom, $prenom, $dateNaissance, $commentaire, $statut, $taille, $poids)
    {
        $this->numLicense = $numLicense;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->dateNaissance = $dateNaissance;
        $this->commentaire = $commentaire;
        $this->statut = $statut;
        $this->taille = $taille;
        $this->poids = $poids;
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
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * @param mixed $dateNaissance
     */
    public function setDateNaissance($dateNaissance): void
    {
        $this->dateNaissance = $dateNaissance;
    }

    /**
     * @return mixed
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * @param mixed $commentaire
     */
    public function setCommentaire($commentaire): void
    {
        $this->commentaire = $commentaire;
    }

    /**
     * @return mixed
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * @param mixed $statut
     */
    public function setStatut($statut): void
    {
        $this->statut = $statut;
    }

    /**
     * @return mixed
     */
    public function getTaille()
    {
        return $this->taille;
    }

    /**
     * @param mixed $taille
     */
    public function setTaille($taille): void
    {
        $this->taille = $taille;
    }

    /**
     * @return mixed
     */
    public function getPoids()
    {
        return $this->poids;
    }

    /**
     * @param mixed $poids
     */
    public function setPoids($poids): void
    {
        $this->poids = $poids;
    }

}