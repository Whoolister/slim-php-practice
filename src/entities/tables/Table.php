<?php

declare(strict_types=1);

namespace App\entities\tables;

use JsonSerializable;

readonly class Table implements JsonSerializable
{
    /**
     * Constructs a Table
     *
     * @param TableStatus $status Status of the table.
     * @param bool $active Whether the table is active or not, defaults to true.
     * @param string $id ID of the Table, defaults to an empty string.
     */
    public function __construct(public TableStatus $status, public bool $active = true, public string $id = '')
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'active' => $this->active
        ];
    }
}