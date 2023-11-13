<?php

declare(strict_types=1);

namespace App\repositories\orders;

use App\entities\orders\OrderItem;
use App\repositories\CrudRepository;
use DateTime;
use PDO;

/**
 * Repository for order items.
 *
 * @implements CrudRepository<OrderItem, int>
 */
final readonly class OrderItemRepository implements CrudRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    public function __construct(private PDO $connection)
    {
    }

    /**
     * @return int Number of order items in the database.
     */
    public function count(): int
    {
        if (($statement = $this->connection->query('SELECT COUNT(*) FROM order_items')) === false) {
            return 0;
        }

        return (int) $statement->fetchColumn();
    }

    /**
     * @return OrderItem[] All the order items in the database.
     */
    public function getAll(): array
    {
        if (($statement = $this->connection->query('SELECT order_id, product_id, id FROM order_items')) === false) {
            return [];
        }

        $result = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[] = $this->map($row);
        }

        return $result;
    }

    /**
     * Gets an order item by its ID.
     *
     * @param int $id The ID of the order item to get.
     * @return false|OrderItem  The order item with the given ID, or false if it couldn't be found.
     */
    public function getById($id): false|OrderItem
    {
        if (($statement = $this->connection->prepare('SELECT order_id, product_id, id FROM order_items WHERE id = :id')) === false) {
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

    /**
     * Checks if an order item with the given ID exists.
     *
     * @param int $id The ID of the order item to check.
     * @return bool Whether the order item exists.
     */
    public function existsById($id): bool
    {
        if (($statement = $this->connection->prepare('SELECT COUNT(*) FROM order_items WHERE id = :id')) === false) {
            return false;
        }

        if (!$statement->execute(['id' => $id])) {
            return false;
        }

        return (bool) $statement->fetchColumn();
    }

    /**
     * Saves an order item to the database.
     *
     * @param OrderItem $entity The order item to save.
     * @return false|OrderItem The saved order item, or false if it couldn't be saved.
     */
    public function save($entity): false|OrderItem
    {
        if (($statement = $this->connection->prepare(
            'INSERT INTO order_items (order_id, product_id, start_time, end_time) 
                    VALUES (:order_id, :product_id, :start_time, :end_time)
                    ON DUPLICATE KEY UPDATE order_id = :order_id, product_id = :product_id, start_time = :start_time, end_time = :end_time
                    RETURNING id'
            )) === false
        ) {
            return false;
        }

        if ($statement->execute([
            'order_id' => $entity->getOrderId(),
            'product_id' => $entity->getProductId(),
            'start_time' => $entity->getStartTime()?->format(self::DATE_FORMAT),
            'end_time' => $entity->getEndTime()?->format(self::DATE_FORMAT),
        ])) {
            $statement->fetchColumn()
            return $this->getById($id);
        } else {
            return false;
        }
    }

    /**
     * Deletes an order item from the database.
     *
     * @param int $id The ID of the order item to delete.
     * @return bool Whether the order item was deleted.
     */
    public function deleteById($id): bool
    {
        if (($statement = $this->connection->prepare('DELETE FROM order_items WHERE id = :id')) === false) {
            return false;
        }

        return $statement->execute(['id' => $id]) && $statement->rowCount() !== 0;
    }

    /**
     * Creates an order item object from an array.
     *
     * @param array{ order_id: int, product_id: int, start_time: ?string, end_time: ?string, id: int } $row The order item to create, in array form.
     * @return OrderItem The order item mapped from the given row.
     */
    private function map(array $row): OrderItem
    {
        $startTime = DateTime::createFromFormat(self::DATE_FORMAT, $row['start_time']);
        $endTime = DateTime::createFromFormat(self::DATE_FORMAT, $row['end_time']);

        return new OrderItem(
            (int) $row['order_id'],
            (int) $row['product_id'],
            ($startTime === false) ? null : $startTime,
            ($endTime === false) ? null : $endTime,
            (int) $row['id'],
        );
    }
}