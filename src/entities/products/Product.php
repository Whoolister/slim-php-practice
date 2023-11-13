<?php

declare(strict_types=1);

namespace App\entities\products;

use App\entities\Entity;
use function is_string;

final class Product extends Entity
{
    private string $name;
    private float $price;
    private int $estimatedTime;
    private ProductType $type;
    private bool $active;

    /**
     * @return string Name of the Product.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name Name of the Product.
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return float Price of the Product.
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price Price of the Product.
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return int Estimated time in seconds to prepare the Product.
     */
    public function getEstimatedTime(): int
    {
        return $this->estimatedTime;
    }

    /**
     * @param int $estimatedTime Estimated time in seconds to prepare the Product.
     */
    public function setEstimatedTime(int $estimatedTime): void
    {
        $this->estimatedTime = $estimatedTime;
    }

    /**
     * @return ProductType Type of the Product.
     */
    public function getType(): ProductType
    {
        return $this->type;
    }

    /**
     * @param string|ProductType $type Type of the Product.
     */
    public function setType(string|ProductType $type): void
    {
        $this->type = is_string($type) ? ProductType::from($type) : $type;
    }

    /**
     * @return bool Whether the Product is active or not.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active Whether the Product is active or not.
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return ?int ID of the Product.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param ?int $id ID of the Product.
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

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
    public function __construct(string $name, float $price, int $estimatedTime, ProductType $type, bool $active = true, ?int $id = null)
    {
        $this->setName($name);
        $this->setPrice($price);
        $this->setEstimatedTime($estimatedTime);
        $this->setType($type);
        $this->setActive($active);
        $this->setId($id);
    }

    /**
     * @return array{ nombre: string, precio: float, tiempoEstimado: int, tipo: string, activo: bool, id: ?int } Array representation of the Product.
     */
    public function jsonSerialize(): array
    {
        return [
            'nombre' => $this->getName(),
            'precio' => $this->getPrice(),
            'tiempoEstimado' => $this->getEstimatedTime(),
            'tipo' => $this->getType()->value,
            'activo' => $this->isActive(),
            'id' => $this->getId(),
        ];
    }
}