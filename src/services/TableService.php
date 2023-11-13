<?php

declare(strict_types=1);

namespace App\services;

use App\entities\tables\Table;
use App\entities\tables\TableStatus;
use App\repositories\tables\TableRepository;

final readonly class TableService
{
    /**
     * Constructs a Table Service.
     *
     * @param TableRepository $tableRepository The repository to use for database operations.
     */
    public function __construct(private TableRepository $tableRepository)
    {
    }

    /**
     * @return Table[] All the tables currently persisted.
     */
    public function getAll(): array
    {
        return $this->tableRepository->getAll();
    }

    /**
     * Gets a table by its ID.
     *
     * @param string $id The ID of the table to get.
     * @return false|Table The table with the given ID, or false if it couldn't be found.
     */
    public function getOne(string $id): false|Table
    {
        return $this->tableRepository->getById($id);
    }

    /**
     * Persists a Table.
     *
     * @param Table $table The table to persist.
     * @return false|Table The created table, or false if it couldn't be created.
     */
    public function add(Table $table): false|Table
    {
        if ($table->getId() !== null) {
            return false;
        }

        return $this->tableRepository->save($table);
    }

    /**
     * Updates a table.
     *
     * @param Table $table The table to update.
     * @return false|Table The updated table, or false if it couldn't be updated.
     */
    public function update(Table $table): false|Table
    {
        if (!$this->tableRepository->existsById($table->getId())) {
            return false;
        }

        return $this->tableRepository->save($table);
    }

    /**
     * Deletes a table.
     *
     * @param string $id The ID of the table to delete.
     * @return bool Whether the table was deleted successfully.
     */
    public function delete(string $id): bool
    {
        return $this->tableRepository->deleteById($id);
    }
}