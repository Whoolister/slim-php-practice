<?php

declare(strict_types=1);

namespace App\entities\orders;

use App\entities\Entity;
use function trim;

final class Order extends Entity
{
    private string $tableId;
    private string $clientName;

    /**
     * @return string ID of the Table.
     */
    public function getTableId(): string
    {
        return $this->tableId;
    }

    /**
     * @param string $tableId ID of the Table.
     */
    public function setTableId(string $tableId): void
    {
        $this->tableId = $tableId;
    }

    /**
     * @return string Name of the Client that made the order.
     */
    public function getClientName(): string
    {
        return $this->clientName;
    }

    /**
     * @param string $clientName Name of the Client that made the order.
     */
    public function setClientName(string $clientName): void
    {
        $this->clientName = $clientName;
    }

    /**
     * @return int ID of the Order.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id ID of the Order.
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Constructs an Order.
     *
     * @param string $tableId ID of the Table.
     * @param string $clientName Name of the Client that made the order.
     * @param int $id ID of the Order, defaults to 0.
     */
    public function __construct(string $tableId, string $clientName, int $id = 0)
    {
        $this->setTableId($tableId);
        $this->setClientName($clientName);
        $this->setId($id);
    }

    /**
     * @return array{ idMesa: string, nombreCliente: string, id: int } Array representation of the Order.
     */
    public function jsonSerialize(): array
    {
        return [
            'idMesa' => $this->getTableId(),
            'nombreCliente' => $this->getClientName(),
            'id' => $this->getId(),
        ];
    }
}