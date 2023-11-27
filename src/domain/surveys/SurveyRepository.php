<?php

declare(strict_types=1);

namespace App\domain\surveys;

use App\domain\CrudRepository;
use PDO;

/**
 * @implements CrudRepository<Survey, int>
 */
class SurveyRepository implements CrudRepository
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @return int Number of surveys stored.
     */
    public function count(): int
    {
        $statement = $this->connection->query('SELECT COUNT(*) FROM surveys');

        $statement->execute();

        return (int) $statement->fetchColumn();
    }

    /**
     * @return Survey[] All the surveys in the database.
     */
    public function getAll(): array
    {
        $statement = $this->connection->query('SELECT id, order_id, table_rating, restaurant_rating, waiter_rating, chef_rating, comment FROM surveys');

        $statement->execute();

        $surveysData = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($surveyData) => $this->map($surveyData), $surveysData);
    }

    /**
     * Gets a survey by its ID.
     *
     * @param int $id The ID of the survey to get.
     * @return false|Survey The survey with the given ID, or false if it doesn't exist.
     */
    public function getById($id): false|Survey
    {
        $statement = $this->connection->prepare(
            'SELECT id, order_id, table_rating, restaurant_rating, waiter_rating, chef_rating, comment 
                    FROM surveys 
                    WHERE id = :id'
        );

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        $statement->execute([':id' => $id]);

        $surveyData = $statement->fetch(PDO::FETCH_ASSOC);

        if ($surveyData === false) {
            return false;
        }

        return $this->map($surveyData);
    }

    /**
     * Checks whether a survey with the given ID exists.
     *
     * @param int $id The ID of the survey to check.
     * @return bool Whether the survey was saved successfully.
     */
    public function existsById($id): bool
    {
        $statement = $this->connection->prepare('SELECT COUNT(*) FROM surveys WHERE id = :id');

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        $statement->execute();

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
        $statement = $this->connection->prepare(
            'INSERT INTO surveys (id, order_id, table_rating, restaurant_rating, waiter_rating, chef_rating, comment) 
                    VALUES (:id, :order_id, :table_rating, :restaurant_rating, :waiter_rating, :chef_rating, :comment)
                    ON DUPLICATE KEY UPDATE order_id = :order_id, table_rating = :table_rating, restaurant_rating = :restaurant_rating, waiter_rating = :waiter_rating, chef_rating = :chef_rating, comment = :comment'
        );

        $statement->bindValue(':id', $entity->id, PDO::PARAM_INT);
        $statement->bindValue(':order_id', $entity->orderId);
        $statement->bindValue(':table_rating', $entity->tableRating);
        $statement->bindValue(':restaurant_rating', $entity->restaurantRating);
        $statement->bindValue(':waiter_rating', $entity->waiterRating);
        $statement->bindValue(':chef_rating', $entity->chefRating);
        $statement->bindValue(':comment', $entity->comment);

        if ($statement->execute() === false) {
            return false;
        }

        return $this->getById($entity->id ?? $this->connection->lastInsertId());
    }

    /**
     * Soft-deletes a survey from the database.
     *
     * @param int $id The ID of the survey to delete.
     * @return bool Whether the survey was deleted successfully.
     */
    public function deleteById($id): bool
    {
        $statement = $this->connection->prepare('DELETE FROM surveys WHERE id = :id');

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        return $statement->execute() && $statement->rowCount() !== 0;
    }

    protected function map(array $row): Survey
    {
        return new Survey(
            $row['order_id'],
            (int) $row['table_rating'],
            (int) $row['restaurant_rating'],
            (int) $row['waiter_rating'],
            (int) $row['chef_rating'],
            $row['comment'],
            (int) $row['id']
        );
    }
}