<?php

namespace dao;
use Participation;
class DaoParticipation extends Dao
{

    /**
     * @param Participation $elt
     * @return void
     */
    public function create($elt)
    {
        if (!$elt instanceof Participation) {
            throw new \InvalidArgumentException("L'élément doit être une instance de Joueur");
        }

        $sql = "INSERT INTO MATCHS (numLicense, dateMatch, heure, estTitulaire, evaluation, poste) 
                VALUES (:numLicense, :dateMatch, :heure, :estTitulaire, :evaluation,:poste)";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':numLicense' => $elt->getNumLicense(),
            ':dateMatch' => $elt->getDateMatch(),
            ':heure' => $elt->getHeure(),
            ':estTitulaire' => $elt->getEstTitulaire(),
            ':evaluation' => $elt->getEvaluation(),
            ':poste' => $elt->getPoste()
        ]);
    }

    /**
     * @param Participation $elt
     * @return void
     */
    public function update($elt)
    {
        $sql = "UPDATE MATCHS SET numLicense = :numLicense, dateMatch = :dateMatch, heure = :heure, 
                estTitulaire = :estTitulaire, evaluation = :evaluation, poste = :poste
                WHERE numLicense = :numLicense AND dateMatch = :dateMatch AND heure = :heure";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':numLicense' => $elt->getNumLicense(),
            ':dateMatch' => $elt->getDateMatch(),
            ':heure' => $elt->getHeure(),
            ':estTitulaire' => $elt->getEstTitulaire(),
            ':evaluation' => $elt->getEvaluation(),
            ':poste' => $elt->getPoste()
        ]);
    }

    /**
     * @param Participation $elt
     * @return void
     */
    public function delete($elt)
    {
        $sql = "DELETE FROM MATCHS WHERE numLicense = :numLicense AND dateMatch = :dateMatch AND heure = :heure";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':numLicense' => $elt->getNumLicense(),
            ':dateMatch' => $elt->getDateMatch(),
            ':heure' => $elt->getHeure()
        ]);
    }

    /**
     * @param ...$id
     * @return Participation
     */
    public function findById(...$id): Participation
    {
        if (empty($id[0]) && empty($id[1]) && empty($id[2])) {
            throw new \InvalidArgumentException("Un numéro de licence et une date et une heure sont requis");
        }
        $numLicense = $id[0];
        $dateMatch = $id[1];
        $heure = $id[2];
        $sql = "SELECT * FROM PARTICIPATION WHERE numLicense = :numLicense AND dateMatch = :dateMatch AND heure = :heure";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':numLicense' => $numLicense,
            ':dateMatch' => $dateMatch,
            ':heure' => $heure
        ]);
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM PARTICIPATION";
        $statement = $this->pdo->query($sql);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}