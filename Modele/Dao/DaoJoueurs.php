<?php

namespace Modele\Dao;
use Modele\Joueurs;
require_once __DIR__ . '/Dao.php';
require_once __DIR__ . '/../Joueurs.php';

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

        $sql = "INSERT INTO JOUEURS (numLicense, nom, prenom, dateNaissance, commentaire, statut, taille, poids) 
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
        $sql = "DELETE FROM PARTICIPATION WHERE numLicense = :numLicense;
                DELETE FROM JOUEURS WHERE numLicense = :numLicense";
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

        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            throw new \RuntimeException("Aucun joueur trouvé avec ce numéro de licence.");
        }

        return $this->creerInstance($data);
    }


    /**
     * @return array
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM JOUEURS";
        $statement = $this->pdo->query($sql);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $joueurs = [];

        foreach ($results as $data) {
            $joueurs[] = $this->creerInstance($data);
        }

        return $joueurs;
    }

    public function creerInstance($data): Joueurs
    {
        if (!$data) {
            throw new \RuntimeException("Les données fournies pour créer une instance de Joueurs sont invalides.");
        }

        return new Joueurs(
            $data['numLicense'],
            $data['nom'],
            $data['prenom'],
            $data['dateNaissance'],
            $data['commentaire'],
            $data['statut'],
            $data['taille'],
            $data['poids']
        );
    }

    public function getPostePrefere($numLicense) {
        $sql = "SELECT poste, COUNT(*) AS occurrences 
            FROM PARTICIPATION 
            WHERE numLicense = :numLicense 
            GROUP BY poste 
            ORDER BY occurrences DESC 
            LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['numLicense' => $numLicense]);
        return $stmt->fetch(\PDO::FETCH_ASSOC)['poste'] ?? null;
    }


    public function getTotalTitulaire($numLicense) {
        $sql = "SELECT COUNT(*) AS total_titulaire 
            FROM PARTICIPATION 
            WHERE numLicense = :numLicense AND estTitulaire = TRUE";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['numLicense' => $numLicense]);
        return $stmt->fetch(\PDO::FETCH_ASSOC)['total_titulaire'] ?? 0;
    }


    public function getTotalRemplacant($numLicense) {
        $sql = "SELECT COUNT(*) AS total_remplacant 
            FROM PARTICIPATION 
            WHERE numLicense = :numLicense AND estTitulaire = FALSE";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['numLicense' => $numLicense]);
        return $stmt->fetch(\PDO::FETCH_ASSOC)['total_remplacant'] ?? 0;
    }


    public function getMoyenneEndurance($numLicense) {
        $sql = "SELECT AVG(endurance) AS moyenne_endurance 
            FROM PARTICIPATION 
            WHERE numLicense = :numLicense";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['numLicense' => $numLicense]);
        return $stmt->fetch(\PDO::FETCH_ASSOC)['moyenne_endurance'] ?? null;
    }

    public function getMoyenneVitesse($numLicense) {
        $sql = "SELECT AVG(vitesse) AS moyenne_vitesse 
            FROM PARTICIPATION 
            WHERE numLicense = :numLicense";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['numLicense' => $numLicense]);
        return $stmt->fetch(\PDO::FETCH_ASSOC)['moyenne_vitesse'] ?? null;
    }

    public function getMoyenneDefense($numLicense) {
        $sql = "SELECT AVG(defense) AS moyenne_defense 
            FROM PARTICIPATION 
            WHERE numLicense = :numLicense";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['numLicense' => $numLicense]);
        return $stmt->fetch(\PDO::FETCH_ASSOC)['moyenne_defense'] ?? null;
    }

    public function getMoyenneTirs($numLicense) {
        $sql = "SELECT AVG(tirs) AS moyenne_tirs 
            FROM PARTICIPATION 
            WHERE numLicense = :numLicense";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['numLicense' => $numLicense]);
        return $stmt->fetch(\PDO::FETCH_ASSOC)['moyenne_tirs'] ?? null;
    }

    public function getMoyennePasses($numLicense) {
        $sql = "SELECT AVG(passes) AS moyenne_passes 
            FROM PARTICIPATION 
            WHERE numLicense = :numLicense";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['numLicense' => $numLicense]);
        return $stmt->fetch(\PDO::FETCH_ASSOC)['moyenne_passes'] ?? null;
    }

    public function getPourcentageMatchsGagnes($numLicense) {
        $sqlVictoires = "SELECT COUNT(*) AS total_victoires 
                     FROM PARTICIPATION, MATCHS 
                     WHERE PARTICIPATION.numLicense = :numLicense 
                       AND PARTICIPATION.dateMatch = MATCHS.dateMatch 
                       AND PARTICIPATION.heure = MATCHS.heure 
                       AND (
                            (MATCHS.lieuRencontre = 'Domicile' AND MATCHS.scoreEquipeDomicile > MATCHS.scoreEquipeExterne) 
                            OR 
                            (MATCHS.lieuRencontre = 'Externe' AND MATCHS.scoreEquipeDomicile < MATCHS.scoreEquipeExterne)
                       )";
        $stmtVictoires = $this->pdo->prepare($sqlVictoires);
        $stmtVictoires->execute(['numLicense' => $numLicense]);
        $totalVictoires = $stmtVictoires->fetch(\PDO::FETCH_ASSOC)['total_victoires'] ?? 0;

        $sqlTotalMatchs = "SELECT COUNT(*) AS total_matchs 
                       FROM PARTICIPATION 
                       WHERE numLicense = :numLicense";
        $stmtMatchs = $this->pdo->prepare($sqlTotalMatchs);
        $stmtMatchs->execute(['numLicense' => $numLicense]);
        $totalMatchs = $stmtMatchs->fetch(\PDO::FETCH_ASSOC)['total_matchs'] ?? 0;

        return $totalMatchs > 0 ? ($totalVictoires * 100.0) / $totalMatchs : 0;
    }


    public function getSelectionsConsecutives($numLicense) {
        $sql = "
        WITH CTE AS (
            SELECT 
                dateMatch,
                ROW_NUMBER() OVER (ORDER BY dateMatch) AS rn1,
                ROW_NUMBER() OVER (PARTITION BY numLicense ORDER BY dateMatch) AS rn2
            FROM PARTICIPATION 
            WHERE numLicense = :numLicense
        )
        SELECT MAX(consecutive_count) AS selections_consecutives
        FROM (
            SELECT COUNT(*) AS consecutive_count
            FROM CTE
            GROUP BY rn1 - rn2
        ) AS consecutive_groups;
    ";
        $stmt = $this->pdo->prepare($sql); // Préparation
        $stmt->execute(['numLicense' => $numLicense]); // Exécution
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['selections_consecutives'] ?? 0;
    }

    public function hasParticipatedInMatches($numLicense) {
        // Préparer la requête pour vérifier si le joueur a des matchs
        $query = "SELECT COUNT(*) FROM PARTICIPATION WHERE num_license = :numLicense";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':numLicense', $numLicense, \PDO::PARAM_STR);
        $stmt->execute();

        // Récupérer le nombre de matchs
        $count = $stmt->fetchColumn();

        // Retourner vrai si le joueur a participé à des matchs, sinon faux
        return $count > 0;
    }

}
