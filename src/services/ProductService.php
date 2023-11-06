<?php

declare(strict_types=1);

namespace App\services;

use App\entities\products\Product;
use App\entities\products\ProductType;
use App\repositories\ProductRepository;

readonly class ProductService
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
    public function GetAll(): array
    {
        return $this->productRepository->GetAll();
    }

    /**
     * Gets a product by its ID.
     *
     * @param int $id The ID of the product to get.
     * @return false|Product The product with the given ID, or false if it doesn't exist.
     */
    public function GetById(int $id): false|Product
    {
        return $this->productRepository->GetById($id);
    }

    /**
     * Persists a product.
     *
     * @param string $name The name of the product.
     * @param float $price The price of the product.
     * @param int $estimatedTime The estimated time in minutes.
     * @param ProductType $type The type of the product.
     * @return false|Product The created product, or false if it couldn't be created.
     */
    public function Add(string $name, float $price, int $estimatedTime, ProductType $type): false|Product
    {
        $product = new Product($name, $price, $estimatedTime, $type);

        return $this->productRepository->Add($product) ? $product : false;
    }

    /**
     * Updates a product.
     *
     * @param int $id The ID of the product to update.
     * @param string $name The new name of the product.
     * @param float $price The new price of the product.
     * @param int $estimatedTime The new estimated time in seconds.
     * @param ProductType $type The new type of the product.
     * @param bool $active Whether the product is active.
     * @return false|Product The updated product, or false if it couldn't be updated.
     */
    public function Update(int $id, string $name, float $price, int $estimatedTime, ProductType $type, bool $active): false|Product
    {
        $product = new Product($name, $price, $estimatedTime, $type, $active, $id);

        return $this->productRepository->Update($product) ? $product : false;
    }

    /**
     * Deletes a product.
     *
     * @param int $id The ID of the product to delete.
     * @return bool Whether the product was deleted successfully.
     */
    public function Delete(int $id): bool
    {
        return $this->productRepository->Delete($id);
    }
}