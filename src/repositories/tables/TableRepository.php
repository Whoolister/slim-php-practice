<?php

declare(strict_types=1);

namespace App\repositories\tables;

use App\entities\tables\Table;
use App\repositories\CrudRepository;
use PDO;
use function filter_var;
use const FILTER_VALIDATE_BOOLEAN;

/**
 * Repository for tables.
 *
 * @implements CrudRepository<Table, string>
 */
final readonly class TableRepository implements CrudRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function count(): int
    {
        if (($statement = $this->connection->query('SELECT COUNT(*) FROM tables')) === false) {
            return 0;
        }

        return (int) $statement->fetchColumn();
    }

    /**
     * @return Table[] All the tables in the database.
     */
    public function getAll(): array
    {
        if (($statement = $this->connection->query('SELECT * FROM tables')) === false) {
            return [];
        }

        $result = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[] = $this->map($row);
        }

        return $result;
    }

    /**
     * Gets a table by its ID.
     *
     * @param string $id The ID of the table to get.
     * @return false|Table The table with the given ID, or false if it doesn't exist.
     */
    public function getById($id): false|Table
    {
        if (($statement = $this->connection->prepare('SELECT * FROM tables WHERE id = :id')) === false) {
            return false;
        }

        if ($statement->execute(['id' => $id]) === false) {
            return false;
        }

        if (($table = $statement->fetch(PDO::FETCH_ASSOC)) === false) {
            return false;
        }

        return $this->map($table);
    }

    /**
     * Checks whether a table with the given ID exists.
     *
     * @param string $id The ID of the table to check.
     * @return bool Whether a table with the given ID exists.
     */
    public function existsById($id): bool
    {
        if (($statement = $this->connection->prepare('SELECT COUNT(*) FROM tables WHERE id = :id')) === false) {
            return false;
        }

        if ($statement->execute(['id' => $id]) === false) {
            return false;
        }

        return (bool) $statement->fetchColumn();
    }

    /**
     * Saves a table to the database
     *
     * @param Table $entity The table to save.
     * @return false|Table The saved table, or false if it couldn't be saved.
     */
    public function save($entity): false|Table {
        if (($statement = $this->connection->prepare('INSERT INTO tables (id, status, active) VALUES (:id, :status, :active) ON DUPLICATE KEY UPDATE status = :status, active = :active')) === false) {
            return false;
        }

        if ($statement->execute([
            'id' => $entity->getId(),
            'status' => $entity->getStatus(),
        ])) {
            return $entity;
        } else {
            return false;
        }
    }

    /**
     * Soft-deletes a table from the database.
     *
     * @param string $id The ID of the table to delete.
     * @return bool Whether the table was deleted successfully.
     */
    public function deleteById($id): bool
    {
        if (($statement = $this->connection->prepare('UPDATE tables SET active = false WHERE id = :id')) === false) {
            return false;
        }

        return $statement->execute(['id' => $id]) && $statement->rowCount() !== 0;
    }

    /**
     * Creates a table object from an array.
     *
     * @param array{ status: string, active: bool, id: string } $row The table to create, in array form.
     * @return Table The table object.
     */
    protected function map(array $row): Table
    {
        return new Table(
            $row['status'],
            filter_var($row['active'], FILTER_VALIDATE_BOOLEAN),
            $row['id']
        );
    }
}