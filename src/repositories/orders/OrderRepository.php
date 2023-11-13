<?php

declare(strict_types=1);

namespace App\repositories\orders;

use App\entities\orders\Order;
use App\repositories\CrudRepository;
use PDO;

/**
 * Repository for orders.
 *
 * @implements CrudRepository<Order, int>
 */
final readonly class OrderRepository implements CrudRepository
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @return int Number of orders in the database.
     */
    public function count(): int
    {
        if (($statement = $this->connection->query('SELECT COUNT(*) FROM orders')) === false) {
            return 0;
        }

        return (int) $statement->fetchColumn();
    }

    /**
     * @return Order[] All the orders in the database.
     */
    public function getAll(): array
    {
        if (($statement = $this->connection->query('SELECT id, table_id, client_name FROM orders')) === false) {
            return [];
        }

        $result = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[] = $this->map($row);
        }

        return $result;
    }

    /**
     * Gets all the orders from a table.
     *
     * @param string $tableId The ID of the table to get the orders from.
     * @return Order[] All the orders in the database.
     */
    public function getAllByTableId(string $tableId): array
    {
        if (($statement = $this->connection->prepare('SELECT id, table_id, client_name FROM orders WHERE table_id = :table_id')) === false) {
            return [];
        }

        if (!$statement->execute(['table_id' => $tableId])) {
            return [];
        }

        $result = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[] = $this->map($row);
        }

        return $result;
    }

    /**
     * Gets an order by its ID.
     *
     * @param int $id The ID of the order to get.
     * @return false|Order The order with the given ID, or false if it doesn't exist.
     */
    public function getById($id): false|Order
    {
        if (($statement = $this->connection->prepare('SELECT id, table_id, client_name FROM orders WHERE id = :id')) === false) {
            return false;
        }

        if (!$statement->execute(['id' => $id])) {
            return false;
        }

        if (($row = $statement->fetch(PDO::FETCH_ASSOC)) === false) {
            return false;
        }

        return $this->map($row);
    }

    public function getByTableId() {

    }

    /**
     * Checks whether an order with the given ID exists.
     *
     * @param int $id The ID of the order to check.
     * @return bool Whether an order with the given ID exists.
     */
    public function existsById($id): bool
    {
        if (($statement = $this->connection->prepare('SELECT COUNT(*) FROM orders WHERE id = :id')) === false) {
            return false;
        }

        if (!$statement->execute(['id' => $id])) {
            return false;
        }

        return (bool) $statement->fetchColumn();
    }

    /**
     * Saves an order to the database.
     *
     * @param Order $entity The order to save.
     * @return false|Order The saved order, or false if it couldn't be saved.
     */
    public function save($entity): false|Order
    {
        if (($statement = $this->connection->prepare(
            'INSERT INTO orders (table_id, client_name) 
                    VALUES (:table_id, :client_name)
                    ON DUPLICATE KEY UPDATE table_id = :table_id, client_name = :client_name'
            )) === false
        ) {
            return false;
        }

        if ($statement->execute([
            'table_id' => $entity->getTableId(),
            'client_name' => $entity->getClientName(),
        ])) {
            return $entity;
        } else {
            return false;
        }
    }

    /**
     * Deletes an order from the database.
     *
     * @param int $id The ID of the order to delete.
     * @return bool Whether the order was deleted.
     */
    public function deleteById($id): bool
    {
        if (($statement = $this->connection->prepare('DELETE FROM orders WHERE id = :id')) === false) {
            return false;
        }

        return $statement->execute(['id' => $id]) && $statement->rowCount() !== 0;
    }

    /**
     * Creates an order object from an array.
     *
     * @param array{ table_id: string, client_name: string, id: int } $row The order to create, in array form.
     * @return Order The order object.
     */
    protected function map(array $row): Order
    {
        return new Order(
            $row['table_id'],
            $row['client_name'],
            (int) $row['id'],
        );
    }
}