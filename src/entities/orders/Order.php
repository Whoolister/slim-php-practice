<?php

declare(strict_types=1);

namespace App\entities\orders;

use JsonSerializable;

readonly class Order implements JsonSerializable
{
    /**
     * Constructs an Order.
     *
     * @param string $tableId ID of the Table.
     * @param int $id ID of the Order, defaults to 0.
     */
    public function __construct(public string $tableId, public int $id = 0)
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'idMesa' => $this->tableId,
        ];
    }
}