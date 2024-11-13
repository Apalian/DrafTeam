<?php
class Joueurs
{
    private $numLicense;
    private $nom;
    private $prenom;
    private $dateNaissance;
    private $commentaire;
    private $statut;
    private $taille;
    private $poids;

    public function __construct($numLicense,$nom,$prenom,$dateNaissance,$commentaire,$statut,$taille,$poids){
        $this->numLicense = $numLicense;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->dateNaissance = $dateNaissance;
        $this->commentaire = $commentaire;
        $this->statut = $statut;
        $this->taille = $taille;
        $this->poids = $poids;
    }
}