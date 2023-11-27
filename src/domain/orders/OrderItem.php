<?php

declare(strict_types=1);

namespace App\domain\orders;

use App\domain\Entity;
use DateTime;

/**
 * @template-extends Entity<int>
 */
class OrderItem extends Entity
{
    private const TIME_FORMAT = 'Y-m-d H:i:s';

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
        public string       $orderId,
        public int       $productId,
        public ?DateTime $startTime = null,
        public ?DateTime $endTime = null,
        ?int       $id = null
    ) {
        parent::__construct($id);
    }

    /**
     * @return OrderItemStatus Status of the OrderItem.
     */
    public function getStatus(): OrderItemStatus
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
     * @inheritdoc Entity::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        $json = [
            'id' => $this->id,
            'orderId' => $this->orderId,
            'productId' => $this->productId,
        ];

        if ($this->startTime !== null) {
            $json['horaInicio'] = $this->startTime->format(self::TIME_FORMAT);
        }

        if ($this->endTime !== null) {
            $json['horaFin'] = $this->endTime->format(self::TIME_FORMAT);
        }

        return $json;
    }
}