<?php

namespace dao;
use Joueurs;

class DaoJoueurs extends Dao
{

    /**
     * @param Joueurs $elt
     * @return void
     */
    public function create($elt)
    {
        if (!$elt instanceof Joueurs) {
            throw new \InvalidArgumentException("L'élément doit être une instance de Joueur");
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
     * @param Joueurs $elt
     * @return void
     */
    public function delete($elt)
    {
        $sql = "DELETE FROM JOUEURS WHERE numLicense = :numLicense";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([':numLicense' => $elt->getNumLicense()]);
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
        return $statement->fetch(\PDO::FETCH_ASSOC);
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
}
