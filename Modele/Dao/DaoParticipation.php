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
            throw new \InvalidArgumentException("L'élément doit être une instance de Participation");
        }

        $sql = "INSERT INTO PARTICIPATION (numLicense, dateMatch, heure, estTitulaire, endurance, vitesse, defense, tirs, passes, poste) 
                VALUES (:numLicense, :dateMatch, :heure, :estTitulaire, :endurance, :vitesse, :defense, :tirs, :passes, :poste)";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':numLicense' => $elt->getNumLicense(),
            ':dateMatch' => $elt->getDateMatch(),
            ':heure' => $elt->getHeure(),
            ':estTitulaire' => $elt->getEstTitulaire(),
            ':endurance' => $elt->getEndurance(),
            ':vitesse' => $elt->getVitesse(),
            ':defense' => $elt->getDefense(),
            ':tirs' => $elt->getTirs(),
            ':passes' => $elt->getPasses(),
            ':poste' => $elt->getPoste()
        ]);
    }

    /**
     * @param Participation $elt
     * @return void
     */
    public function update($elt)
    {
        if (!$elt instanceof Participation) {
            throw new \InvalidArgumentException("L'élément doit être une instance de Participation");
        }

        $sql = "UPDATE PARTICIPATION SET estTitulaire = :estTitulaire, endurance = :endurance, vitesse = :vitesse, 
                defense = :defense, tirs = :tirs, passes = :passes, poste = :poste
                WHERE numLicense = :numLicense AND dateMatch = :dateMatch AND heure = :heure";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':numLicense' => $elt->getNumLicense(),
            ':dateMatch' => $elt->getDateMatch(),
            ':heure' => $elt->getHeure(),
            ':estTitulaire' => $elt->getEstTitulaire(),
            ':endurance' => $elt->getEndurance(),
            ':vitesse' => $elt->getVitesse(),
            ':defense' => $elt->getDefense(),
            ':tirs' => $elt->getTirs(),
            ':passes' => $elt->getPasses(),
            ':poste' => $elt->getPoste()
        ]);
    }

    /**
     * @param ...$id
     * @return Participation
     */
    public function findById(...$id): Participation
    {
        if (empty($id[0]) || empty($id[1]) || empty($id[2])) {
            throw new \InvalidArgumentException("Un numéro de licence, une date et une heure sont requis");
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
            throw new \RuntimeException("Aucune participation trouvée pour ces critères");
        }

        return $this->creerInstance($data);
    }

    /**
     * @param $data
     * @return Participation
     */
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
            $data['endurance'],
            $data['vitesse'],
            $data['defense'],
            $data['tirs'],
            $data['passes'],
            $data['poste']
        );
    }

    /**
     * @param string $dateMatch
     * @param string $heure
     * @return array
     */
    public function findByMatch(string $dateMatch, string $heure): array
    {
        if (empty($dateMatch) || empty($heure)) {
            throw new \InvalidArgumentException("Une date et une heure sont requis");
        }

        $sql = "SELECT * FROM PARTICIPATION WHERE dateMatch = :dateMatch AND heure = :heure";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':dateMatch' => $dateMatch,
            ':heure' => $heure
        ]);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $participations = [];
        foreach ($results as $data) {
            $participations[] = $this->creerInstance($data);
        }

        return $participations;
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

    /**
     * @param ...$id
     * @return void
     */
    public function delete(...$id)
    {
        if (empty($id[0]) || empty($id[1]) || empty($id[2])) {
            throw new \InvalidArgumentException("Un numéro de licence, une date et une heure sont requis");
        }

        $numLicense = $id[0];
        $dateMatch = $id[1];
        $heure = $id[2];

        $sql = "DELETE FROM PARTICIPATION WHERE numLicense = :numLicense AND dateMatch = :dateMatch AND heure = :heure";
        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            ':numLicense' => $numLicense,
            ':dateMatch' => $dateMatch,
            ':heure' => $heure
        ]);
    }

    public function deleteByMatch($dateMatch, $heure)
    {
        $sql = "DELETE FROM PARTICIPATION WHERE dateMatch = :dateMatch AND heure = :heure";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':dateMatch' => $dateMatch,
            ':heure' => $heure
        ]);
    }

}
