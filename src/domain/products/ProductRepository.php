<?php

declare(strict_types=1);

namespace App\domain\products;

use App\domain\CrudRepository;
use PDO;
use function array_map;

/**
 * @implements CrudRepository<Product, int>
 */
class ProductRepository implements CrudRepository
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @return int Number of products in the database.
     */
    public function count(): int
    {
        $statement = $this->connection->query('SELECT COUNT(*) FROM products');

        $statement->execute();

        return (int) $statement->fetchColumn();
    }

    /**
     * @return Product[] All the products in the database.
     */
    public function getAll(): array {
        $statement = $this->connection->query('SELECT id, name, price, estimated_time, type, active FROM products');

        $statement->execute();

        $productsData = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($productData) => $this->map($productData), $productsData);
    }

    /**
     * Gets all the products of a given type.
     *
     * @param ProductType $type The type of product to get.
     * @return Product[] All the products in the database of the given type.
     */
    public function getAllByType(ProductType $type): array
    {
        $statement = $this->connection->prepare(
            'SELECT id, name, price, estimated_time, type, active 
                    FROM products 
                    WHERE type = :type'
        );
        
        $statement->bindValue(':type', $type);

        $statement->execute();

        $productsData = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($productData) => $this->map($productData), $productsData);
    }

    /**
     * Gets a product by its ID.
     *
     * @param int $id The ID of the product to get.
     * @return false|Product The product with the given ID, or false if it doesn't exist.
     */
    public function getById($id): false|Product
    {
        $statement = $this->connection->prepare('SELECT id, name, price, estimated_time, type, active FROM products WHERE id = :id');

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        $statement->execute();

        $productData = $statement->fetch(PDO::FETCH_ASSOC);

        if ($productData === false) {
            return false;
        }

        return $this->map($productData);
    }

    /**
     * Checks whether a product with the given ID exists.
     *
     * @param int $id The ID of the product to check.
     * @return bool Whether a product with the given ID exists.
     */
    public function existsById($id): bool
    {
        $statement = $this->connection->prepare('SELECT COUNT(*) FROM products WHERE id = :id');

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        $statement->execute();

        return (bool) $statement->fetchColumn();
    }

    /**
     * Saves a product to the database.
     *
     * @param Product $entity The product to save.
     * @return false|Product The saved product, or false if it couldn't be saved.
     */
    public function save($entity): false|Product {
        $statement = $this->connection->prepare(
            'INSERT INTO products (id, name, price, estimated_time, type) 
                    VALUES (:id, :name, :price, :estimated_time, :type) 
                    ON DUPLICATE KEY UPDATE name = :name, price = :price, estimated_time = :estimated_time, type = :type'
        );

        $statement->bindValue(':id', $entity->id, PDO::PARAM_INT);
        $statement->bindValue(':name', $entity->name);
        $statement->bindValue(':price', $entity->price);
        $statement->bindValue(':estimated_time', $entity->estimatedTime, PDO::PARAM_INT);
        $statement->bindValue(':type', $entity->type->value);
        $statement->bindValue(':active', $entity->active, PDO::PARAM_BOOL);

        if ($statement->execute() === false) {
            return false;
        }

        return $this->getById($entity->id ?? (int) $this->connection->lastInsertId());
    }

    /**
     * Saves multiple products to the database.
     *
     * @param Product[] $entities The products to save.
     * @return bool Whether the products were saved successfully.
     */
    public function saveMultiple(array $entities): bool {
        $this->connection->beginTransaction();

        $statement = $this->connection->prepare(
            'INSERT INTO products (id, name, price, estimated_time, type) 
                    VALUES (:id, :name, :price, :estimated_time, :type) 
                    ON DUPLICATE KEY UPDATE name = :name, price = :price, estimated_time = :estimated_time, type = :type'
        );

        foreach ($entities as $entity) {
            $statement->bindValue(':id', $entity->id, PDO::PARAM_INT);
            $statement->bindValue(':name', $entity->name);
            $statement->bindValue(':price', $entity->price);
            $statement->bindValue(':estimated_time', $entity->estimatedTime, PDO::PARAM_INT);
            $statement->bindValue(':type', $entity->type->value);
            $statement->bindValue(':active', $entity->active, PDO::PARAM_BOOL);

            if ($statement->execute() === false) {
                $this->connection->rollBack();
                return false;
            }
        }

        return $this->connection->commit();
    }

    /**
     * Soft-deletes a product from the database.
     *
     * @param int $id The ID of the product to delete.
     * @return bool Whether the product was deleted successfully.
     */
    public function deleteById($id): bool
    {
        $statement = $this->connection->prepare('UPDATE products SET active = false WHERE id = :id');

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        return $statement->execute(['id' => $id]) && $statement->rowCount() !== 0;
    }

    protected function map(array $row): Product
    {
        return new Product(
            $row['name'],
            (float) $row['price'],
            (int) $row['estimated_time'],
            ProductType::from($row['type']),
            (bool) $row['active'],
            (int) $row['id'],
        );
    }
}