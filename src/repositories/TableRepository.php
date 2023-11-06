<?php

declare(strict_types=1);

namespace App\repositories;

use App\entities\tables\Table;
use PDO;
use function filter_var;
use const FILTER_VALIDATE_BOOLEAN;

readonly class TableRepository
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @return Table[] All the tables in the database.
     */
    public function GetAll(): array
    {
        if (($statement = $this->connection->query('SELECT * FROM tables')) === false) {
            return [];
        }

        $result = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[] = $this->Map($row);
        }

        return $result;
    }

    /**
     * Gets a table by its ID.
     *
     * @param string $id The ID of the table to get.
     * @return false|Table The table with the given ID, or false if it doesn't exist.
     */
    public function GetById(string $id): false|Table
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

        return $this->Map($table);
    }

    /**
     * Adds a table to the database.
     *
     * @param Table $entity The table to add.
     * @return bool Whether the table was added successfully.
     */
    public function Add(Table $entity): bool
    {
        if (($statement = $this->connection->prepare('INSERT INTO tables (status) VALUES (:status)')) === false) {
            return false;
        }

        return $statement->execute([
            'status' => $entity->status->value,
        ]);
    }

    /**
     * Updates a table in the database.
     *
     * @param Table $entity The table to update.
     * @return bool Whether the table was updated successfully.
     */
    public function Update(Table $entity): bool
    {
        if (($statement = $this->connection->prepare('UPDATE tables SET status = :status, active = :active WHERE id = :id')) === false) {
            return false;
        }

        return $statement->execute([
            'status' => $entity->status,
            'active' => $entity->active,
            'id' => $entity->id
        ]);
    }

    /**
     * Soft-deletes a table from the database.
     *
     * @param string $id The ID of the table to delete.
     * @return bool Whether the table was deleted successfully.
     */
    public function Delete(string $id): bool
    {
        if (($statement = $this->connection->prepare('UPDATE tables SET active = false WHERE id = :id')) === false) {
            return false;
        }

        return $statement->execute(['id' => $id]);
    }

    /**
     * Creates a table object from an array.
     *
     * @param array{ status: string, active: bool, id: string } $row The table to create, in array form.
     * @return Table The table object.
     */
    protected function Map(array $row): Table
    {
        return new Table(
            $row['status'],
            filter_var($row['active'], FILTER_VALIDATE_BOOLEAN),
            $row['id']
        );
    }
}