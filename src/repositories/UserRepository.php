<?php

declare(strict_types=1);

namespace App\repositories;

use App\entities\users\User;
use App\entities\users\UserRole;
use PDO;
use function filter_var;
use const FILTER_VALIDATE_BOOLEAN;

readonly class UserRepository
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @return User[] All the users in the database.
     */
    public function GetAll(): array
    {
        if (($statement = $this->connection->query('SELECT id, first_name, last_name, email, password, role, active FROM users')) === false) {
            return [];
        }

        $result = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[] = $this->Map($row);
        }

        return $result;
    }

    /**
     * Gets a user by its ID.
     *
     * @param int $id The ID of the user to get.
     * @return false|User The user with the given ID, or false if it doesn't exist.
     */
    public function GetById(int $id): false|User
    {
        if (($statement = $this->connection->prepare('SELECT id, first_name, last_name, email, password, role, active FROM users WHERE id = :id')) === false) {
            return false;
        }

        if ($statement->execute([':id' => $id]) === false) {
            return false;
        }

        if (($row = $statement->fetch(PDO::FETCH_ASSOC)) === false) {
            return false;
        }

        return $this->Map($row);
    }

    /**
     * Adds a user to the database.
     *
     * @param User $entity The user to add.
     * @return bool Whether the user was added successfully.
     */
    public function Add(User $entity): bool
    {
        if (($statement = $this->connection->prepare('INSERT INTO users (first_name, last_name, email, password, role) VALUES (:first_name, :last_name, :email, :password, :role)')) === false) {
            return false;
        }

        return $statement->execute([
            'first_name' => $entity->firstName,
            'last_name' => $entity->lastName,
            'email' => $entity->email,
            'password' => $entity->password,
            'role' => $entity->role->value,
        ]);
    }

    /**
     * Updates a user in the database.
     *
     * @param User $entity The user to update.
     * @return bool Whether the user was updated successfully.
     */
    public function Update(User $entity): bool
    {
        if (($statement = $this->connection->prepare('UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, password = :password, role = :role, active = :active WHERE id = :id')) === false) {
            return false;
        }

        return $statement->execute([
            'first_name' => $entity->firstName,
            'last_name' => $entity->lastName,
            'email' => $entity->email,
            'password' => $entity->password,
            'role' => $entity->role->value,
            'active' => $entity->active,
            'id' => $entity->id,
        ]);
    }

    /**
     * Soft-deletes a user from the database.
     *
     * @param int $id The ID of the user to delete.
     * @return bool Whether the user was deleted successfully.
     */
    public function Delete(int $id): bool
    {
        if (($statement = $this->connection->prepare('UPDATE users SET active = false WHERE id = :id')) === false) {
            return false;
        }

        return $statement->execute(['id' => $id]);
    }

    /**
     * Creates a user object from an array.
     *
     * @param array{ first_name: string, last_name: string, email: string, password: string, role: string, active: bool, id: int } $row
     * @return User The user object.
     */
    protected function Map(array $row): User
    {
        return new User(
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['password'],
            UserRole::from($row['role']),
            filter_var($row['active'], FILTER_VALIDATE_BOOLEAN),
            (int) $row['id']
        );
    }
}