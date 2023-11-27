<?php

declare(strict_types=1);

namespace App\domain\orders;

use App\domain\Entity;

/**
 * @template-extends Entity<string>
 */
class Order extends Entity
{
    /**
     * Constructs an Order.
     *
     * @param int $tableId ID of the Table.
     * @param string $clientName Name of the Client that made the order.
     * @param OrderStatus $status Status of the Order.
     * @param ?string $id ID of the Order, defaults to null.
     */
    public function __construct(
        public int $tableId,
        public string $clientName,
        public OrderStatus $status,
        ?string $id = null
    ) {
        parent::__construct($id);
    }

    /**
     * @inheritdoc Entity::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        return [
            'idMesa' => $this->tableId,
            'nombreCliente' => $this->clientName,
            'estado' => $this->status->value,
            'id' => $this->id,
        ];
    }
}