<?php

declare(strict_types=1);

namespace App\domain\orders;

use App\domain\CrudRepository;
use App\domain\products\ProductType;
use DateTime;
use PDO;
use function array_map;

/**
 * @implements CrudRepository<OrderItem, int>
 */
class OrderItemRepository implements CrudRepository
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
        $statement = $this->connection->query('SELECT COUNT(*) FROM order_items');

        $statement->execute();

        return (int) $statement->fetchColumn();
    }

    /**
     * @return OrderItem[] All the order items in the database.
     */
    public function getAll(): array
    {
        $statement = $this->connection->query('SELECT id, order_id, product_id, start_time, end_time FROM order_items');

        $statement->execute();

        $orderItemsData = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($orderItemData) => $this->map($orderItemData), $orderItemsData);
    }

    /**
     * Gets all the pending order items with a given type.
     *
     * @param ProductType $productType The type of product to get the order items for.
     * @return OrderItem[] All the pending order items of the given type.
     */
    public function getAllPendingByType(ProductType $productType): array
    {
        $statement = $this->connection->prepare(
            "SELECT id, order_id, product_id, start_time, end_time 
                    FROM order_items 
                    WHERE product_id IN ( SELECT id FROM products WHERE type = :type )
                    AND (status = 'PENDIENTE' OR status = 'PREPARANDO')"
        );

        $statement->bindValue(':type', $productType->value);

        $statement->execute();

        $orderItemsData = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($orderItemData) => $this->map($orderItemData), $orderItemsData);
    }

    /**
     * @return OrderItem[] All the pending order items.
     */
    public function getAllPending(): array
    {
        $statement = $this->connection->query(
            "SELECT id, order_id, product_id, start_time, end_time
                    FROM order_items 
                    WHERE status = 'PENDIENTE' OR status = 'PREPARANDO'"
        );

        $statement->execute();

        $orderItemsData = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($orderItemData) => $this->map($orderItemData), $orderItemsData);
    }

    /**
     * Gets all the order items for a given order.
     *
     * @param string $orderId The ID of the order to get the order items for.
     * @return OrderItem[] All the order items for the given order.
     */
    public function getAllByOrderId(string $orderId): array
    {
        $statement = $this->connection->prepare(
            'SELECT id, order_id, product_id, start_time, end_time
                    FROM order_items 
                    WHERE order_id = :order_id'
        );

        $statement->bindValue(':order_id', $orderId);

        $statement->execute();

        $orderItemsData = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($orderItemData) => $this->map($orderItemData), $orderItemsData);
    }

    /**
     * Gets an order item by its ID.
     *
     * @param int $id The ID of the order item to get.
     * @return false|OrderItem  The order item with the given ID, or false if it couldn't be found.
     */
    public function getById($id): false|OrderItem
    {
        $statement = $this->connection->prepare('SELECT id, order_id, product_id, start_time, end_time  FROM order_items WHERE id = :id');

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        $statement->execute();

        $orderItemData = $statement->fetch(PDO::FETCH_ASSOC);

        if ($orderItemData === false) {
            return false;
        }

        return $this->map($orderItemData);
    }

    /**
     * Gets the price of an order.
     *
     * @param string $orderId The ID of the order to get the price for.
     * @return float The price of the order.
     */
    public function getPrice(string $orderId): float
    {
        $statement = $this->connection->prepare(
            'SELECT SUM(price) FROM order_items
                    INNER JOIN products ON product_id = products.id
                    WHERE order_id = :order_id'
        );

        $statement->bindValue(':order_id', $orderId);

        $statement->execute();

        return (float) $statement->fetchColumn();

    }

    /**
     * Gets the pending time for an order item.
     *
     * @param string $orderId The ID of the order to get the pending time for.
     * @return int The pending time in seconds for the order item.
     */
    public function getPendingTime(string $orderId): int
    {
        $statement = $this->connection->prepare(
            "SELECT MAX(
                        (SELECT estimated_time FROM products WHERE id = product_id)
                    ) FROM order_items
                    WHERE order_id = :order_id 
                    AND status != 'LISTO'"
        );

        $statement->bindValue(':order_id', $orderId);

        $statement->execute();

        return (int) $statement->fetchColumn();
    }

    /**
     * Checks if an order item with the given ID exists.
     *
     * @param int $id The ID of the order item to check.
     * @return bool Whether the order item exists.
     */
    public function existsById($id): bool
    {
        $statement = $this->connection->prepare('SELECT COUNT(*) FROM order_items WHERE id = :id');

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        $statement->execute(['id' => $id]);

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
        $statement = $this->connection->prepare(
            'INSERT INTO order_items (id, order_id, product_id, start_time, end_time) 
                    VALUES (:id, :order_id, :product_id, :start_time, :end_time)
                    ON DUPLICATE KEY UPDATE order_id = :order_id, product_id = :product_id, start_time = :start_time, end_time = :end_time'
        );

        $statement->bindValue(':id', $entity->id, PDO::PARAM_INT);
        $statement->bindValue(':order_id', $entity->orderId);
        $statement->bindValue(':product_id', $entity->productId, PDO::PARAM_INT);
        $statement->bindValue(':start_time', $entity->startTime?->format(self::DATE_FORMAT));
        $statement->bindValue(':end_time', $entity->endTime?->format(self::DATE_FORMAT));

        if ($statement->execute() === false) {
            return false;
        }

        return $this->getById($entity->id ?? (int) $this->connection->lastInsertId());
    }

    /**
     * Saves multiple order items to the database.
     *
     * @param OrderItem[] $entities The order items to save.
     * @return bool Whether the order items were saved or not.
     */
    public function saveMultiple(array $entities): bool
    {
        $this->connection->beginTransaction();

        $statement = $this->connection->prepare(
            'INSERT INTO order_items (id, order_id, product_id, start_time, end_time) 
                    VALUES (:id, :order_id, :product_id, :start_time, :end_time)
                    ON DUPLICATE KEY UPDATE order_id = :order_id, product_id = :product_id, start_time = :start_time, end_time = :end_time'
        );

        foreach ($entities as $entity) {
            $statement->bindValue(':id', $entity->id, PDO::PARAM_INT);
            $statement->bindValue(':order_id', $entity->orderId);
            $statement->bindValue(':product_id', $entity->productId, PDO::PARAM_INT);
            $statement->bindValue(':start_time', $entity->startTime?->format(self::DATE_FORMAT));
            $statement->bindValue(':end_time', $entity->endTime?->format(self::DATE_FORMAT));

            if ($statement->execute() === false) {
                $this->connection->rollBack();
                return false;
            }
        }

        return $this->connection->commit();
    }

    /**
     * Deletes an order item from the database.
     *
     * @param int $id The ID of the order item to delete.
     * @return bool Whether the order item was deleted.
     */
    public function deleteById($id): bool
    {
        $statement = $this->connection->prepare('DELETE FROM order_items WHERE id = :id');

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        return $statement->execute() && $statement->rowCount() !== 0;
    }

    /**
     * Deletes all the order items for an order.
     *
     * @param string $orderId The ID of the order to delete the items for.
     * @return bool Whether the order items were deleted or not.
     */
    public function deleteByOrderId(string $orderId): bool
    {
        $statement = $this->connection->prepare('DELETE FROM order_items WHERE order_id = :order_id');

        $statement->bindValue(':order_id', $orderId);

        return $statement->execute() && $statement->rowCount() !== 0;
    }

    private function map(array $row): OrderItem
    {
        return new OrderItem(
            $row['order_id'],
            (int) $row['product_id'],
            $row['start_time'] === null ? null : DateTime::createFromFormat(self::DATE_FORMAT, $row['start_time']),
            $row['end_time'] === null ? null : DateTime::createFromFormat(self::DATE_FORMAT, $row['end_time']),
            (int) $row['id'],
        );
    }
}