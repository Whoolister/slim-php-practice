<?php

declare(strict_types=1);

namespace App\entities\tables;

use App\entities\Entity;
use function is_string;

final class Table extends Entity
{
    private TableStatus $status;
    private bool $active;

    /**
     * @return TableStatus Status of the table.
     */
    public function getStatus(): TableStatus
    {
        return $this->status;
    }

    /**
     * @param string|TableStatus $status Status of the table.
     */
    public function setStatus(string|TableStatus $status): void
    {
        $this->status = is_string($status)? TableStatus::from($status) : $status;
    }

    /**
     * @return bool Whether the table is active or not.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active Whether the table is active or not.
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return ?string ID of the Table.
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param ?string $id ID of the Table.
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * Constructs a Table
     *
     * @param TableStatus $status Status of the table.
     * @param bool $active Whether the table is active or not, defaults to true.
     * @param ?string $id ID of the Table, defaults to an empty string.
     */
    public function __construct(TableStatus $status, bool $active = true, ?string $id = null)
    {
        $this->setStatus($status);
        $this->setActive($active);
        $this->setId($id);
    }

    /**
     * @return array{ estado: string, activo: bool, id: ?string } Array representation of the Table.
     */
    public function jsonSerialize(): array
    {
        return [
            'estado' => $this->getStatus()->value,
            'activo' => $this->isActive(),
            'id' => $this->getId(),
        ];
    }
}