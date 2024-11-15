<?php

namespace Modele\Dao;
use Modele\Matchs;
require_once __DIR__ . '/Dao.php';
class DaoMatchs extends Dao
{

    /**
     * @param Matchs $elt
     * @return void
     */
    public function create($elt)
    {
        if (!$elt instanceof Matchs) {
            throw new \InvalidArgumentException("L'élément doit être une instance de Joueur");
        }

        $sql = "INSERT INTO MATCHS (dateMatch, heure, nomEquipeAdverse, lieuRencontre, scoreEquipeDomicile, scoreEquipeExterne) 
                VALUES (:dateMatch, :heure, :nomEquipeAdverse, :lieuRencontre, :scoreEquipeDomicile, :scoreEquipeExterne)";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':dateMatch' => $elt->getDateMatch(),
            ':heure' => $elt->getHeure(),
            ':lieuRencontre' => $elt->getLieuRencontre(),
            ':scoreEquipeDomicile' => $elt->getScoreEquipeDomicile(),
            ':scoreEquipeExterne' => $elt->getScoreEquipeExterne()
        ]);
    }

    /**
     * @param Matchs $elt
     * @return void
     */
    public function update($elt)
    {
        $sql = "UPDATE MATCHS SET dateMatch = :dateMatch, heure = :heure, lieuRencontre = :lieuRencontre, 
                scoreEquipeDomicile = :scoreEquipeDomicile, scoreEquipeExterne = :scoreEquipeExterne
                WHERE dateMatch = :dateMatch AND heure = :heure";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':dateMatch' => $elt->getDateMatch(),
            ':heure' => $elt->getHeure(),
            ':lieuRencontre' => $elt->getLieuRencontre(),
            ':scoreEquipeDomicile' => $elt->getScoreEquipeDomicile(),
            ':scoreEquipeExterne' => $elt->getScoreEquipeExterne()
        ]);
    }

    /**
     * @param ...$id
     * @return void
     */
    public function delete(...$id)
    {
        if (empty($id[0]) && empty($id[1])) {
            throw new \InvalidArgumentException("Une date et une heure sont requis");
        }
        $dateMatch = $id[0];
        $heure = $id[1];
        $sql = "DELETE FROM MATCHS WHERE dateMatch = :dateMatch AND heure = :heure";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':dateMatch' => $dateMatch,
            ':heure' => $heure
        ]);
    }

    /**
     * @param ...$id
     * @return Matchs
     */
    public function findById(...$id): Matchs
    {
        if (empty($id[0]) && empty($id[1])) {
            throw new \InvalidArgumentException("Une date et une heure sont requis");
        }
        $dateMatch = $id[0];
        $heure = $id[1];
        $sql = "SELECT * FROM JOUEURS WHERE dateMatch = :dateMatch AND heure = :heure";
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            ':dateMatch' => $dateMatch,
            ':heure' => $heure
        ]);
        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            throw new \RuntimeException("Aucun matchs trouvé à cette date et heure");
        }

        return $this->creerInstance($data);
    }

    /**
     * @return array
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM MATCHS";
        $statement = $this->pdo->query($sql);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $matchs = [];

        foreach ($results as $data) {
            $matchs[] = $this->creerInstance($data);
        }

        return $matchs;
    }

    public function creerInstance($data): Matchs
    {
        if (!$data) {
            throw new \RuntimeException("Les données fournies pour créer une instance de Matchs sont invalides.");
        }

        return new Matchs(
            $data['dateMatch'],
            $data['heure'],
            $data['nomEquipeAdverse'],
            $data['lieuRencontre'],
            $data['ScoreEquipeDomicile'],
            $data['scoreEquipeExterne']
        );
    }
}