<?php

namespace Modele\Dao;
use Modele\Participation;
require_once __DIR__ . '/Dao.php';
require_once __DIR__ . '/../Participation.php';

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
     * @param ...$id
     * @return void
     */
    public function delete(...$id)
    {
        if (empty($id[0]) && empty($id[1]) && empty($id[2])) {
            throw new \InvalidArgumentException("Un numéro de licence et une date et une heure sont requis");
        }
        $numLicense = $id[0];
        $dateMatch = $id[1];
        $heure = $id[2];
        $sql = "DELETE FROM MATCHS WHERE numLicense = :numLicense AND dateMatch = :dateMatch AND heure = :heure";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':numLicense' => $numLicense,
            ':dateMatch' => $dateMatch,
            ':heure' => $heure
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
        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            throw new \RuntimeException("Aucun participation trouvé pour ce joueur à cette date et heure");
        }

        return $this->creerInstance($data);
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM PARTICIPATION";
        $statement = $this->pdo->query($sql);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $participations = [];

        foreach ($results as $data) {
            $participations[] = $this->creerInstance($data);
        }

        return $participations;
    }

    public function creerInstance($data): Participation
    {
        if (!$data) {
            throw new \RuntimeException("Les données fournies pour créer une instance de Participation sont invalides.");
        }

        return new Participation(
            $data['numLicense'],
            $data['dateMatch'],
            $data['heure'],
            $data['estTitulaire'],
            $data['evaluation'],
            $data['poste']
        );
    }
}