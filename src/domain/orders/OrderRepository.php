<?php

declare(strict_types=1);

namespace App\domain\orders;

use App\domain\CrudRepository;
use PDO;
use function array_map;
use function json_encode;
use function md5;
use function random_bytes;
use function substr;
use function uniqid;

/**
 * @implements CrudRepository<Order, string>
 */
class OrderRepository implements CrudRepository
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @return int Number of orders in the database.
     */
    public function count(): int
    {
        $statement = $this->connection->query('SELECT COUNT(*) FROM orders');

        $statement->execute();

        return (int) $statement->fetchColumn();
    }

    /**
     * @return Order[] All the orders in the database.
     */
    public function getAll(): array
    {
        $statement = $this->connection->query('SELECT id, table_id, client_name, status FROM orders');

        $statement->execute();

        $ordersData = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($orderData) => $this->map($orderData), $ordersData);
    }

    /**
     * Gets all the orders with a given status.
     *
     * @param OrderStatus $status The status to get the orders for.
     * @return Order[] All the orders with the given status.
     */
    public function getAllByStatus(OrderStatus $status): array
    {
        $statement = $this->connection->prepare(
            'SELECT id, table_id, client_name, status 
                    FROM orders
                    WHERE status = :status'
        );

        $statement->bindValue(':status', $status->value);

        $statement->execute();

        $ordersData = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($orderData) => $this->map($orderData), $ordersData);
    }

    /**
     * Gets an order by its ID.
     *
     * @param string $id The ID of the order to get.
     * @return false|Order The order with the given ID, or false if it doesn't exist.
     */
    public function getById($id): false|Order
    {
        $statement = $this->connection->prepare(
            'SELECT id, table_id, client_name, status
                    FROM orders 
                    WHERE id = :id'
        );

        $statement->bindValue(':id', $id);

        $statement->execute();

        $orderData = $statement->fetch(PDO::FETCH_ASSOC);

        if ($orderData === false) {
            return false;
        }

        return $this->map($orderData);
    }

    /**
     * Gets the currently active order for the given table.
     *
     * @param int $tableId The ID of the table to get the order for.
     * @return false|Order The currently active order for the given table, or false if there is none.
     */
    public function getActiveOrderByTableId(int $tableId): false|Order
    {
        $statement = $this->connection->prepare(
            "SELECT id, table_id, client_name, status FROM orders 
                    WHERE table_id = :table_id 
                    AND status != 'PAGADO'"
        );

        $statement->bindValue(':table_id', $tableId);

        $statement->execute();

        $orderData = $statement->fetch(PDO::FETCH_ASSOC);

        if ($orderData === false) {
            return false;
        }

        return $this->map($orderData);
    }

    /**
     * Checks whether an order with the given ID exists.
     *
     * @param string $id The ID of the order to check.
     * @return bool Whether an order with the given ID exists.
     */
    public function existsById($id): bool
    {
        $statement = $this->connection->prepare('SELECT COUNT(*) FROM orders WHERE id = :id');

        $statement->bindValue(':id', $id);

        $statement->execute();

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
        $statement = $this->connection->prepare(
            'INSERT INTO orders (id, table_id, client_name, status) 
                    VALUES (:id, :table_id, :client_name, :status)
                    ON DUPLICATE KEY UPDATE table_id = :table_id, client_name = :client_name, status = :status'
        );

        $id = $entity->id ?? substr(md5(random_bytes(5)), 0, 5);

        $statement->bindValue(':id', $id);
        $statement->bindValue(':table_id', $entity->tableId);
        $statement->bindValue(':client_name', $entity->clientName);
        $statement->bindValue(':status', $entity->status->value);

        if ($statement->execute() === false) {
            return false;
        }

        return $this->getById($id);
    }

    /**
     * Deletes an order from the database.
     *
     * @param string $id The ID of the order to delete.
     * @return bool Whether the order was deleted.
     */
    public function deleteById($id): bool
    {
        $statement = $this->connection->prepare('DELETE FROM orders WHERE id = :id');

        $statement->bindValue(':id', $id);

        return $statement->execute() && $statement->rowCount() !== 0;
    }

    protected function map(array $row): Order
    {
        return new Order(
            tableId: $row['table_id'],
            clientName: $row['client_name'],
            status: OrderStatus::from($row['status']),
            id: $row['id'],
        );
    }
}