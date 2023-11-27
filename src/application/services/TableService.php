<?php

declare(strict_types=1);

namespace App\application\services;

use App\domain\exceptions\DomainException;
use App\domain\tables\Table;
use App\domain\tables\TableRepository;
use App\domain\tables\TableStatus;

class TableService
{
    private TableRepository $tableRepository;

    /**
     * Constructs a Table Service.
     *
     * @param TableRepository $tableRepository The repository to use for database operations.
     */
    public function __construct(TableRepository $tableRepository)
    {
        $this->tableRepository = $tableRepository;
    }

    /**
     * @return Table[] All the tables currently persisted.
     */
    public function getAll(): array
    {
        return $this->tableRepository->getAll();
    }

    /**
     * Gets all the persisted tables with the given status.
     *
     * @param TableStatus $status The status to filter by.
     * @return Table[] All the tables with the given status.
     */
    public function getAllByStatus(TableStatus $status): array
    {
        return $this->tableRepository->getAllByStatus($status);
    }

    /**
     * @return false|Table The most popular table, or false if there are no tables.
     */
    public function getMostPopular(): false|Table {
        return $this->tableRepository->getMostPopular();
    }

    /**
     * Gets a table by its ID.
     *
     * @param int $id The ID of the table to get.
     * @return false|Table The table with the given ID, or false if it couldn't be found.
     */
    public function getById(int $id): false|Table
    {
        return $this->tableRepository->getById($id);
    }

    /**
     * Creates a table.
     *
     * @param Table $table The table to save.
     * @return Table The saved table.
     * @throws DomainException If the table could not be saved.
     */
    public function save(Table $table): Table
    {
        if (($result = $this->tableRepository->save($table)) === false) {
            throw new DomainException('No se pudo guardar la mesa', 500);
        }

        return $result;
    }

    /**
     * Deletes a table.
     *
     * @param int $id The ID of the table to delete.
     * @return bool Whether the table was deleted successfully.
     * @throws DomainException If the table does not exist.
     */
    public function delete(int $id): bool
    {
        if ($this->tableRepository->existsById($id) === false) {
            throw new DomainException('No se encontró la mesa.', 404);
        }

        return $this->tableRepository->deleteById($id);
    }
}