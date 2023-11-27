<?php

declare(strict_types=1);

namespace App\domain\tables;

use App\domain\Entity;

/**
 * @template-extends Entity<int>
 */
class Table extends Entity
{
    /**
     * Constructs a Table
     *
     * @param TableStatus $status Status of the table.
     * @param bool $active Whether the table is active or not, defaults to true.
     * @param ?int $id ID of the Table, defaults to null.
     */
    public function __construct(
        public TableStatus $status,
        public bool $active = true,
        ?int $id = null
    ) {
        parent::__construct($id);
    }

    /**
     * @inheritdoc Entity::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        return [
            'estado' => $this->status->value,
            'activo' => $this->active,
            'id' => $this->id,
        ];
    }
}