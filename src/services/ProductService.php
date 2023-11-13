<?php

declare(strict_types=1);

namespace App\services;

use App\entities\products\Product;
use App\entities\products\ProductType;
use App\repositories\products\ProductRepository;

final readonly class ProductService
{
    /**
     * Constructs a Product Service.
     *
     * @param ProductRepository $productRepository The product repository to use.
     */
    public function __construct(private ProductRepository $productRepository)
    {
    }

    /**
     * @return Product[] All the products currently persisted.
     */
    public function getAll(): array
    {
        return $this->productRepository->getAll();
    }

    /**
     * Gets a product by its ID.
     *
     * @param int $id The ID of the product to get.
     * @return false|Product The product with the given ID, or false if it doesn't exist.
     */
    public function GetOne(int $id): false|Product
    {
        return $this->productRepository->getById($id);
    }

    /**
     * Persists a product.
     *
     * @param Product $product The product to persist.
     * @return false|Product The created product, or false if it couldn't be created.
     */
    public function add(Product $product): false|Product
    {
        if ($product->getId() !== null) {
            return false;
        }

        return $this->productRepository->save($product);
    }

    /**
     * Updates a product.
     *
     * @param Product $product The product to update.
     * @return false|Product The updated product, or false if it couldn't be updated.
     */
    public function update(Product $product): false|Product
    {
        if (!$this->productRepository->existsById($product->getId())) {
            return false;
        }

        return $this->productRepository->save($product);
    }

    /**
     * Deletes a product.
     *
     * @param int $id The ID of the product to delete.
     * @return bool Whether the product was deleted successfully.
     */
    public function delete(int $id): bool
    {
        return $this->productRepository->deleteById($id);
    }
}