<?php

namespace Modele\Dao;
use Modele\Joueurs;
require_once __DIR__ . '/Dao.php';

class DaoJoueurs extends Dao
{

    /**
     * @param Joueurs $elt
     * @return void
     */
    public function create($elt)
    {
        if (!$elt instanceof Joueurs) {
            throw new \InvalidArgumentException("L'élément doit être une instance de Joueurs");
        }

        $sql = "INSERT INTO MATCHS (numLicense, nom, prenom, dateNaissance, commentaire, statut, taille, poids) 
                VALUES (:numLicense, :nom, :prenom, :dateNaissance, :commentaire, :statut, :taille, :poids)";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':numLicense' => $elt->getNumLicense(),
            ':nom' => $elt->getNom(),
            ':prenom' => $elt->getPrenom(),
            ':dateNaissance' => $elt->getDateNaissance(),
            ':commentaire' => $elt->getCommentaire(),
            ':statut' => $elt->getStatut(),
            ':taille' => $elt->getTaille(),
            ':poids' => $elt->getPoids()
        ]);
    }

    /**
     * @param Joueurs $elt
     * @return void
     */
    public function update($elt)
    {
        $sql = "UPDATE JOUEURS SET nom = :nom, prenom = :prenom, dateNaissance = :dateNaissance, 
                commentaire = :commentaire, statut = :statut, taille = :taille, poids = :poids 
                WHERE numLicense = :numLicense";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':numLicense' => $elt->getNumLicense(),
            ':nom' => $elt->getNom(),
            ':prenom' => $elt->getPrenom(),
            ':dateNaissance' => $elt->getDateNaissance(),
            ':commentaire' => $elt->getCommentaire(),
            ':statut' => $elt->getStatut(),
            ':taille' => $elt->getTaille(),
            ':poids' => $elt->getPoids()
        ]);
    }

    /**
     * @param ...$id
     * @return void
     */
    public function delete(...$id)
    {
        if (empty($id[0])) {
            throw new \InvalidArgumentException("Un numéro de licence est requis.");
        }
        $numLicense = $id[0];
        $sql = "DELETE FROM JOUEURS WHERE numLicense = :numLicense";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([':numLicense' => $numLicense]);
    }

    /**
     * @param ...$id
     * @return Joueurs
     */
    public function findById(...$id): Joueurs
    {
        if (empty($id[0])) {
            throw new \InvalidArgumentException("Un numéro de licence est requis.");
        }
        $numLicense = $id[0];
        $sql = "SELECT * FROM JOUEURS WHERE numLicense = :numLicense";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([':numLicense' => $numLicense]);
        return creerInstance($statement->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM JOUEURS";
        $statement = $this->pdo->query($sql);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function creerInstance(...$elt){
        return new Joueurs($elt[0],$elt[1],$elt[2],$elt[3],$elt[4],$elt[5],$elt[6],$elt[7]);
    }
}
