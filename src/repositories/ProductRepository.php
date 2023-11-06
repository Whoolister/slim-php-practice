<?php

declare(strict_types=1);

namespace App\repositories;

use App\entities\products\Product;
use App\entities\products\ProductType;
use PDO;
use function filter_var;
use const FILTER_VALIDATE_BOOLEAN;

readonly class ProductRepository
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @return Product[] All the products in the database.
     */
    public function GetAll(): array {
        if (($statement = $this->connection->query('SELECT id, name, price, estimated_time, type, active FROM products')) === false) {
            return [];
        }

        $result = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[] = $this->Map($row);
        }

        return $result;
    }

    /**
     * Gets a product by its ID.
     *
     * @param int $id The ID of the product to get.
     * @return false|Product The product with the given ID, or false if it doesn't exist.
     */
    public function GetById(int $id): false|Product
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

        return $this->Map($product);
    }

    /**
     * Adds a product to the database.
     *
     * @param Product $entity The product to add.
     * @return bool Whether the product was added successfully.
     */
    public function Add(Product $entity): bool
    {
        if (($statement = $this->connection->prepare('INSERT INTO products (name, price, estimated_time, type) VALUES (:name, :price, :estimated_time, :type)')) === false) {
            return false;
        }

        return $statement->execute([
            'name' => $entity->name,
            'price' => $entity->price,
            'estimated_time' => $entity->estimatedTime,
            'type' => $entity->type->value,
        ]);
    }

    /**
     * Updates a product in the database.
     *
     * @param Product $entity The product to update.
     * @return bool Whether the product was updated successfully.
     */
    public function Update(Product $entity): bool
    {
        if (($statement = $this->connection->prepare('UPDATE products SET name = :name, price = :price, estimated_time = :estimated_time, type = :type, active = :active WHERE id = :id')) === false) {
            return false;
        }

        return $statement->execute([
            'name' => $entity->name,
            'price' => $entity->price,
            'estimated_time' => $entity->estimatedTime,
            'type' => $entity->type->value,
            'active' => $entity->active,
            'id' => $entity->id,
        ]);
    }

    /**
     * Soft-deletes a product from the database.
     *
     * @param int $id The ID of the product to delete.
     * @return bool Whether the product was deleted successfully.
     */
    public function Delete(int $id): bool
    {
        if (($statement = $this->connection->prepare('UPDATE products SET active = false WHERE id = :id')) === false) {
            return false;
        }

        return $statement->execute(['id' => $id]);
    }

    /**
     * Creates a product object from an array.
     *
     * @param array{ name: string, price: float, estimated_time: int, active: bool, type: ProductType, id: int } $row The product to create, in array form.
     * @return Product The product object.
     */
    protected function Map(array $row): Product
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