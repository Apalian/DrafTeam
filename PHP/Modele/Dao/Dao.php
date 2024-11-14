<?php

namespace dao;
use Connexion;
/**
 * @template T
 */
abstract class Dao
{

    protected $pdo;

    public function __construct($username, $password)
    {
        // Obtenir la connexion PDO
        $this->pdo = Connexion::getInstance($username, $password)->getConnection();
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
     * @return void
     */
    public abstract function delete($elt);

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
