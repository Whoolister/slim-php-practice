<?php

declare(strict_types=1);

namespace App\domain;

/**
 * Repository Interface for basic CRUD operations
 *
 * @template T of Entity The type of Entity that this repository stores.
 * @template ID of int|string The type of ID that the Entity uses.
 */
interface CrudRepository
{
    /**
     * @return int Number of entities stored.
     */
    public function count(): int;

    /**
     * @return Entity Array of all stored entities.
     */
    public function getAll(): array;

    /**
     * Gets an entity by its ID.
     *
     * @param ID $id ID of the entity to get.
     * @return false|Entity The entity with the given ID, or false if not found.
     */
    public function getById($id): false|Entity;

    /**
     * Checks whether an entity with the given ID exists.
     *
     * @param ID $id ID of the entity to check.
     * @return bool Whether an entity with the given ID exists.
     */
    public function existsById($id): bool;

    /**
     * Saves an entity.
     *
     * @param Entity $entity Entity to save.
     * @return false|Entity The saved entity, or false if it couldn't be saved.
     */
    public function save(Entity $entity): false|Entity;

    /**
     * Deletes an entity by its ID.
     *
     * @param ID $id ID of the entity to delete.
     * @return bool Whether the entity was deleted.
     */
    public function deleteById($id): bool;
}