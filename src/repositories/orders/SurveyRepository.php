<?php

declare(strict_types=1);

namespace App\repositories\orders;

use App\entities\orders\Survey;
use App\repositories\CrudRepository;
use PDO;

/**
 * Repository for surveys.
 *
 * @implements CrudRepository<Survey, int>
 */
final readonly class SurveyRepository implements CrudRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function count(): int
    {
        if (($statement = $this->connection->query('SELECT COUNT(*) FROM surveys')) === false) {
            return 0;
        }

        return (int) $statement->fetchColumn();
    }

    /**
     * @return Survey[] All the surveys in the database.
     */
    public function getAll(): array
    {
        if (($statement = $this->connection->query('SELECT id, order_id, table_rating, restaurant_rating, waiter_rating, chef_rating, comment FROM surveys')) === false) {
            return [];
        }

        $result = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[] = $this->map($row);
        }

        return $result;
    }

    /**
     * Gets all the surveys for a given order.
     *
     * @param int $orderId The ID of the order to get the surveys for.
     * @return Survey[] All the surveys for the given order.
     */
    public function getAllByOrderId(int $orderId): array
    {
        if (($statement = $this->connection->prepare('SELECT id, order_id, table_rating, restaurant_rating, waiter_rating, chef_rating, comment FROM surveys WHERE order_id = :order_id')) === false) {
            return [];
        }

        if ($statement->execute([':order_id' => $orderId]) === false) {
            return [];
        }

        $result = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[] = $this->map($row);
        }

        return $result;
    }

    /**
     * Gets a survey by its ID.
     *
     * @param $id int The ID of the survey to get.
     * @return false|Survey The survey with the given ID, or false if it doesn't exist.
     */
    public function getById($id): false|Survey
    {
        if (($statement = $this->connection->prepare('SELECT id, order_id, table_rating, restaurant_rating, waiter_rating, chef_rating, comment FROM surveys WHERE id = :id')) === false) {
            return false;
        }

        if ($statement->execute([':id' => $id]) === false) {
            return false;
        }

        if (($row = $statement->fetch(PDO::FETCH_ASSOC)) === false) {
            return false;
        }

        return $this->map($row);
    }

    public function getByIdAndOrderId(int $id, int $orderId): false|Survey
    {
        if (($statement = $this->connection->prepare('SELECT id, order_id, table_rating, restaurant_rating, waiter_rating, chef_rating, comment FROM surveys WHERE id = :id AND order_id = :order_id')) === false) {
            return false;
        }

        if ($statement->execute([':id' => $id, ':order_id' => $orderId]) === false) {
            return false;
        }

        if (($row = $statement->fetch(PDO::FETCH_ASSOC)) === false) {
            return false;
        }

        return $this->map($row);
    }

    /**
     * Checks whether a survey with the given ID exists.
     *
     * @param int $id The ID of the survey to check.
     * @return bool Whether the survey was saved successfully.
     */
    public function existsById($id): bool
    {
        if (($statement = $this->connection->prepare('SELECT COUNT(*) FROM surveys WHERE id = :id')) === false) {
            return false;
        }

        if ($statement->execute([':id' => $id]) === false) {
            return false;
        }

        return (bool) $statement->fetchColumn();
    }

    /**
     * Saves a survey to the database.
     *
     * @param Survey $entity The survey to save.
     * @return false|Survey The saved survey, or false if it couldn't be saved.
     */
    public function save($entity): false|Survey
    {
        if (
            ($statement = $this->connection->prepare(
                'INSERT INTO surveys (id, order_id, table_rating, restaurant_rating, waiter_rating, chef_rating, comment) 
                        VALUES (:id, :order_id, :table_rating, :restaurant_rating, :waiter_rating, :chef_rating, :comment)
                        ON DUPLICATE KEY UPDATE order_id = :order_id, table_rating = :table_rating, restaurant_rating = :restaurant_rating, waiter_rating = :waiter_rating, chef_rating = :chef_rating, comment = :comment'
            )) === false
        ) {
            return false;
        }

        if ($statement->execute([
            ':id' => $entity->getId(),
            ':order_id' => $entity->getOrderId(),
            ':table_rating' => $entity->getTableRating(),
            ':restaurant_rating' => $entity->getRestaurantRating(),
            ':waiter_rating' => $entity->getWaiterRating(),
            ':chef_rating' => $entity->getChefRating(),
            ':comment' => $entity->getComment()
        ])) {
            return $entity;
        } else {
            return false;
        }
    }

    /**
     * Soft-deletes a survey from the database.
     *
     * @param int $id The ID of the survey to delete.
     * @return bool Whether the survey was deleted successfully.
     */
    public function deleteById($id): bool
    {
        if (($statement = $this->connection->prepare('DELETE FROM surveys WHERE id = :id')) === false) {
            return false;
        }

        return $statement->execute([':id' => $id]) && $statement->rowCount() !== 0;
    }

    public function deleteByIdAndOrderId(int $id, int $orderId): bool
    {
        if (($statement = $this->connection->prepare('DELETE FROM surveys WHERE id = :id AND order_id = :order_id')) === false) {
            return false;
        }

        return $statement->execute([':id' => $id, ':order_id' => $orderId]) && $statement->rowCount() !== 0;
    }

    /**
     * Creates a survey object from an array.
     *
     * @param array{ order_id: int, table_rating: int, restaurant_rating: int, waiter_rating: int, chef_rating: int, comment: ?string, id: int } $row The survey to create, in array form.
     * @return Survey The survey object.
     */
    protected function map(array $row): Survey
    {
        return new Survey(
            (int) $row['order_id'],
            (int) $row['table_rating'],
            (int) $row['restaurant_rating'],
            (int) $row['waiter_rating'],
            (int) $row['chef_rating'],
            $row['comment'],
            (int) $row['id']
        );
    }
}