<?php

namespace Modele\Dao;
use Modele\Database;

/**
 * @template T
 */
abstract class Dao
{

    protected $pdo;

    public function __construct($username, $password)
    {
        // Obtenir la connexion PDO
        $this->pdo = Database::getInstance($username, $password)->getConnection();
    }

    /**
     * Creates a new element.
     *
     * @param T $elt
     * @return void
     */
    public abstract function create($elt);

    /**
     * Updates an element.
     *
     * @return void
     */
    public abstract function update($elt);

    /**
     * Deletes an element.
     *
     * @param mixed ...$id
     * @return void
     */
    public abstract function delete(...$id);

    /**
     * Finds an element by its ID(s).
     *
     * @param mixed ...$id
     * @return T|null
     */
    public abstract function findById(...$id);

    /**
     * Finds all elements.
     *
     * @return iterable<T>
     */
    public abstract function findAll(): array;
}
