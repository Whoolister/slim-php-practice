<?php

declare(strict_types=1);

namespace App\domain\products;

use App\domain\Entity;

/**
 * @template-extends Entity<int>
 */
class Product extends Entity
{
    /**
     * Constructs a Product.
     *
     * @param string $name Name of the Product.
     * @param float $price Price of the Product.
     * @param int $estimatedTime Estimated time in seconds to prepare the Product.
     * @param ProductType $type Type of the Product.
     * @param bool $active Whether the Product is active or not, defaults to true.
     * @param ?int $id ID of the Product, defaults to null.
     */
    public function __construct(
        public string $name,
        public float $price,
        public int $estimatedTime,
        public ProductType $type,
        public bool $active = true,
        ?int $id = null)
    {
        parent::__construct($id);
    }

    /**
     * @inheritdoc Entity::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        return [
            'nombre' => $this->name,
            'precio' => $this->price,
            'tiempoEstimado' => $this->estimatedTime,
            'tipo' => $this->type->value,
            'activo' => $this->active,
            'id' => $this->id,
        ];
    }
}