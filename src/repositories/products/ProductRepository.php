<?php

declare(strict_types=1);

namespace App\repositories\products;

use App\entities\products\Product;
use App\entities\products\ProductType;
use App\repositories\CrudRepository;
use PDO;
use function filter_var;
use const FILTER_NULL_ON_FAILURE;
use const FILTER_VALIDATE_BOOLEAN;

/**
 * Repository for products.
 *
 * @implements CrudRepository<Product, int>
 */
final readonly class ProductRepository implements CrudRepository
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @return int Number of products in the database.
     */
    public function count(): int
    {
        if (($statement = $this->connection->query('SELECT COUNT(*) FROM products')) === false) {
            return 0;
        }

        return (int) $statement->fetchColumn();
    }

    /**
     * @return Product[] All the products in the database.
     */
    public function getAll(): array {
        if (($statement = $this->connection->query('SELECT id, name, price, estimated_time, type, active FROM products')) === false) {
            return [];
        }

        $result = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[] = $this->map($row);
        }

        return $result;
    }

    /**
     * Gets a product by its ID.
     *
     * @param int $id The ID of the product to get.
     * @return false|Product The product with the given ID, or false if it doesn't exist.
     */
    public function getById($id): false|Product
    {
        if (($statement = $this->connection->prepare('SELECT id, name, price, estimated_time, type, active FROM products WHERE id = :id')) === false) {
            return false;
        }

        if ($statement->execute(['id' => $id]) === false) {
            return false;
        }

        if (($product = $statement->fetch(PDO::FETCH_ASSOC)) === false) {
            return false;
        }

        return $this->map($product);
    }

    /**
     * Checks whether a product with the given ID exists.
     *
     * @param int $id The ID of the product to check.
     * @return bool Whether a product with the given ID exists.
     */
    public function existsById($id): bool
    {
        if (($statement = $this->connection->prepare('SELECT COUNT(*) FROM products WHERE id = :id')) === false) {
            return false;
        }

        if ($statement->execute(['id' => $id]) === false) {
            return false;
        }

        return (bool) $statement->fetchColumn();
    }

    /**
     * Saves a product to the database.
     *
     * @param Product $entity The product to save.
     * @return false|Product The saved product, or false if it couldn't be saved.
     */
    public function save($entity): false|Product {
        if (
            ($statement = $this->connection->prepare(
                'INSERT INTO products (id, name, price, estimated_time, type) 
                        VALUES (:id, :name, :price, :estimated_time, :type) 
                        ON DUPLICATE KEY UPDATE name = :name, price = :price, estimated_time = :estimated_time, type = :type'
            )) === false
        ) {
            return false;
        }

        if ($statement->execute([
            'id' => $entity->getId(),
            'name' => $entity->getName(),
            'price' => $entity->getPrice(),
            'estimated_time' => $entity->getEstimatedTime(),
            'type' => $entity->getType()->value,
        ])) {
            return $entity;
        } else {
            return false;
        }
    }

    /**
     * Soft-deletes a product from the database.
     *
     * @param int $id The ID of the product to delete.
     * @return bool Whether the product was deleted successfully.
     */
    public function deleteById($id): bool
    {
        if (($statement = $this->connection->prepare('UPDATE products SET active = false WHERE id = :id')) === false) {
            return false;
        }

        return $statement->execute(['id' => $id]) && $statement->rowCount() !== 0;
    }

    /**
     * Creates a product object from an array.
     *
     * @param array{ name: string, price: float, estimated_time: int, active: bool, type: ProductType, id: int } $row The product to create, in array form.
     * @return Product The product object.
     */
    protected function map(array $row): Product
    {
        return new Product(
            $row['name'],
            (float) $row['price'],
            (int) $row['estimated_time'],
            ProductType::from($row['type']),
            filter_var($row['active'], FILTER_VALIDATE_BOOLEAN),
            (int) $row['id'],
        );
    }
}