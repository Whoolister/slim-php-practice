<?php

declare(strict_types=1);

namespace App\entities\orders;

use App\entities\Entity;
use DateTime;

final class OrderItem extends Entity
{
    private int $orderId;
    private int $productId;
    private ?DateTime $startTime;
    private ?DateTime $endTime;

    /**
     * @return int ID of the Order that this item belongs to.
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId ID of the Order that this item belongs to.
     */
    public function setOrderId(int $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @return int ID of the Product that this item is.
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @param int $productId ID of the Product that this item is.
     */
    public function setProductId(int $productId): void
    {
        $this->productId = $productId;
    }

    /**
     * @return ?DateTime Start time of the Order Item.
     */
    public function getStartTime(): ?DateTime
    {
        return $this->startTime;
    }

    /**
     * @param ?DateTime $startTime Start time of the Order Item.
     */
    public function setStartTime(?DateTime $startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return ?DateTime Start time of the Order Item.
     */
    public function getEndTime(): ?DateTime
    {
        return $this->endTime;
    }

    /**
     * @param ?DateTime $endTime End time of the Order Item.
     */
    public function setEndTime(?DateTime $endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * @return OrderItemStatus Status of the OrderItem.
     */
    public function GetStatus(): OrderItemStatus
    {
        if ($this->startTime == null) {
            return OrderItemStatus::PENDING;
        } elseif ($this->endTime == null) {
            return OrderItemStatus::PREPARING;
        } else {
            return OrderItemStatus::READY;
        }
    }

    /**
     * @return ?int ID of the Order Item.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param ?int $id ID of the Order Item.
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Constructs an Order Item.
     *
     * @param int $orderId ID of the Order that this item belongs to.
     * @param int $productId ID of the Product that this item is.
     * @param ?DateTime $startTime Start time of the Order Item, defaults to null if preparation hasn't started.
     * @param ?DateTime $endTime End time of the Order Item, defaults to null if preparation hasn't finished.
     * @param ?int $id ID of the Order Item, defaults to 0.
     */
    public function __construct(
        int       $orderId,
        int       $productId,
        ?DateTime $startTime = null,
        ?DateTime $endTime = null,
        ?int       $id = null
    )
    {
        $this->setOrderId($orderId);
        $this->setProductId($productId);
        $this->setStartTime($startTime);
        $this->setEndTime($endTime);
        $this->setId($id);
    }

    /**
     * @return array{ idPedido: int, idProducto: int, horaInicio: ?string, horaFin: ?string, id: ?int } Array representation of the Order Item.
     */
    public function jsonSerialize(): array
    {
        return [
            'idPedido' => $this->getOrderId(),
            'idProducto' => $this->getProductId(),
            'horaInicio' => $this->getStartTime(),
            'horaFin' => $this->getEndTime(),
            'id' => $this->getId(),
        ];
    }
}