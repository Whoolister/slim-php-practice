<?php

declare(strict_types=1);

namespace App\domain\tables;

use App\domain\CrudRepository;
use PDO;
use function array_map;
use function filter_var;
use const FILTER_VALIDATE_BOOLEAN;

/**
 * @implements CrudRepository<Table, int>
 */
class TableRepository implements CrudRepository
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @return int Number of tables stored.
     */
    public function count(): int
    {
        $statement = $this->connection->query('SELECT COUNT(*) FROM tables');

        $statement->execute();

        return (int) $statement->fetchColumn();
    }

    /**
     * @return Table[] All the tables in the database.
     */
    public function getAll(): array
    {
        $statement = $this->connection->query('SELECT id, status, active FROM tables');

        $statement->execute();

        $tablesData = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($tableData) => $this->map($tableData), $tablesData);
    }

    /**
     * Gets all the persisted tables with the given status.
     *
     * @param TableStatus $status The status to filter by.
     * @return Table[] All the tables with the given status.
     */
    public function getAllByStatus(TableStatus $status): array
    {
        $statement = $this->connection->prepare('SELECT id, status, active FROM tables WHERE status = :status');

        $statement->bindValue(':status', $status->value);

        $statement->execute();

        $tablesData = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($tableData) => $this->map($tableData), $tablesData);
    }

    /**
     * @return false|Table The most popular table, or false if there are no tables.
     */
    public function getMostPopular(): false|Table
    {
        $statement = $this->connection->query(
            'SELECT id, status, active FROM tables 
                    WHERE id = (SELECT table_id FROM orders GROUP BY table_id ORDER BY COUNT(*) DESC LIMIT 1)'
        );

        $statement->execute();

        $tableData = $statement->fetch(PDO::FETCH_ASSOC);

        if ($tableData === false) {
            return false;
        }

        return $this->map($tableData);
    }

    /**
     * Gets a table by its ID.
     *
     * @param int $id The ID of the table to get.
     * @return false|Table The table with the given ID, or false if it doesn't exist.
     */
    public function getById($id): false|Table
    {
        $statement = $this->connection->prepare('SELECT id, status, active FROM tables WHERE id = :id');

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        $statement->execute();

        $tableData = $statement->fetch(PDO::FETCH_ASSOC);

        if ($tableData === false) {
            return false;
        }

        return $this->map($tableData);
    }

    /**
     * Checks whether a table with the given ID exists.
     *
     * @param int $id The ID of the table to check.
     * @return bool Whether a table with the given ID exists.
     */
    public function existsById($id): bool
    {
        $statement = $this->connection->prepare('SELECT COUNT(*) FROM tables WHERE id = :id');

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        $statement->execute(['id' => $id]);

        return (bool) $statement->fetchColumn();
    }

    /**
     * Saves a table to the database
     *
     * @param Table $entity The table to save.
     * @return false|Table The saved table, or false if it couldn't be saved.
     */
    public function save($entity): false|Table {
        $statement = $this->connection->prepare(
            'INSERT INTO tables (id, status) 
                    VALUES (:id, :status) 
                    ON DUPLICATE KEY UPDATE status = :status'
        );

        $statement->bindValue(':id', $entity->id, PDO::PARAM_INT);
        $statement->bindValue(':status', $entity->status->value);

        if ($statement->execute() === false) {
            return false;
        }

        return $this->getById($entity->id ?? (int) $this->connection->lastInsertId());
    }

    /**
     * Soft-deletes a table from the database.
     *
     * @param int $id The ID of the table to delete.
     * @return bool Whether the table was deleted successfully.
     */
    public function deleteById($id): bool
    {
        $statement = $this->connection->prepare('UPDATE tables SET active = false WHERE id = :id');

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        return $statement->execute() && $statement->rowCount() !== 0;
    }

    protected function map(array $row): Table
    {
        return new Table(
            TableStatus::from($row['status']),
            filter_var($row['active'], FILTER_VALIDATE_BOOLEAN),
            (int) $row['id']
        );
    }
}