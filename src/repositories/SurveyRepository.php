<?php

declare(strict_types=1);

namespace App\repositories;

use PDO;

readonly class SurveyRepository
{
    public function __construct(private PDO $connection)
    {
    }

    public function GetAll(): array
    {
        // TODO: Implement GetAll() method.
    }

    public function GetById($id)
    {
        // TODO: Implement GetById() method.
    }

    public function Add($entity): bool
    {
        // TODO: Implement Add() method.
    }

    public function Update($entity): bool
    {
        // TODO: Implement Update() method.
    }

    public function Delete($id): bool
    {
        // TODO: Implement Delete() method.
    }

    protected function Map(array $row)
    {
        // TODO: Implement Map() method.
    }
}