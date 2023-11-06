<?php

declare(strict_types=1);

namespace App\entities\products;

use JsonSerializable;

readonly class Product implements JsonSerializable
{
    /**
     * Constructs a Product.
     *
     * @param string $name Name of the Product.
     * @param float $price Price of the Product.
     * @param int $estimatedTime Estimated time in seconds to prepare the Product.
     * @param ProductType $type Type of the Product.
     * @param bool $active Whether the Product is active or not, defaults to true.
     * @param int $id ID of the Product, defaults to 0.
     */
    public function __construct(
        public string $name,
        public float $price,
        public int $estimatedTime,
        public ProductType $type,
        public bool $active = true,
        public int $id = 0
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->name,
            'precio' => $this->price,
            'tiempoEstimado' => $this->estimatedTime,
            'tipo' => $this->type->value,
            'activo' => $this->active,
        ];
    }
}