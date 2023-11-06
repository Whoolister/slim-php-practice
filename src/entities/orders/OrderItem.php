<?php

declare(strict_types=1);

namespace App\entities\orders;

use DateTime;
use JsonSerializable;

readonly class OrderItem implements JsonSerializable
{
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
     * Constructs an Order Item.
     *
     * @param int $orderId ID of the Order that this item belongs to.
     * @param int $productId ID of the Product that this item is.
     * @param DateTime|null $startTime Start time of the Order Item, defaults to null if preparation hasn't started.
     * @param DateTime|null $endTime End time of the Order Item, defaults to null if preparation hasn't finished.
     * @param int $id ID of the Order Item, defaults to 0.
     */
    public function __construct(
        public int       $orderId,
        public int       $productId,
        public ?DateTime $startTime = null,
        public ?DateTime $endTime = null,
        public int       $id = 0
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'idPedido' => $this->orderId,
            'idProducto' => $this->productId,
            'horaInicio' => $this->startTime,
            'horaFin' => $this->endTime,
        ];
    }
}