<?php

declare(strict_types=1);

namespace App\services;

use App\entities\tables\Table;
use App\entities\tables\TableStatus;
use App\repositories\TableRepository;

readonly class TableService
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
    public function GetAll(): array
    {
        return $this->tableRepository->GetAll();
    }

    /**
     * Gets a table by its ID.
     *
     * @param string $id The ID of the table to get.
     * @return false|Table The table with the given ID, or false if it couldn't be found.
     */
    public function GetById(string $id): false|Table
    {
        return $this->tableRepository->GetById($id);
    }

    /**
     * Persists a Table.
     *
     * @param TableStatus $status The status of the table.
     * @return false|Table The created table, or false if it couldn't be created.
     */
    public function Add(TableStatus $status): false|Table
    {
        $table = new Table($status);

        return $this->tableRepository->Add($table) ? $table : false;
    }

    /**
     * Updates a table.
     *
     * @param string $id The ID of the table to update.
     * @param TableStatus $status The new status of the table.
     * @return false|Table The updated table, or false if it couldn't be updated.
     */
    public function Update(string $id, TableStatus $status, bool $active): false|Table
    {
        $table = new Table($status, $active, $id);

        return $this->tableRepository->Update($table) ? $table : false;
    }

    /**
     * Deletes a table.
     *
     * @param string $id The ID of the table to delete.
     * @return bool Whether the table was deleted successfully.
     */
    public function Delete(string $id): bool
    {
        return $this->tableRepository->Delete($id);
    }
}