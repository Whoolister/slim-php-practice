<?php

declare(strict_types=1);

namespace App\repositories\users;

use App\entities\users\User;
use App\repositories\CrudRepository;
use PDO;
use function boolval;
use function filter_var;
use function is_null;
use const FILTER_VALIDATE_BOOLEAN;

/**
 * Repository for users.
 *
 * @implements CrudRepository<User, int>
 */
final readonly class UserRepository implements CrudRepository
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @return int Number of users in the database.
     */
    public function count(): int
    {
        if (($statement = $this->connection->query('SELECT COUNT(*) FROM users')) === false) {
            return 0;
        }

        return (int) $statement->fetchColumn();
    }

    /**
     * @return User[] All the users in the database.
     */
    public function getAll(): array
    {
        if (($statement = $this->connection->query('SELECT id, first_name, last_name, email, password, role, active FROM users')) === false) {
            return [];
        }

        $result = [];

        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $result[] = $this->map($row);
        }

        return $result;
    }

    /**
     * Gets a user by its ID.
     *
     * @param int $id The ID of the user to get.
     * @return false|User The user with the given ID, or false if it doesn't exist.
     */
    public function getById($id): false|User
    {
        if (($statement = $this->connection->prepare('SELECT id, first_name, last_name, email, password, role, active FROM users WHERE id = :id')) === false) {
            return false;
        }

        if (!$statement->execute([':id' => $id])) {
            return false;
        }

        if (($row = $statement->fetch(PDO::FETCH_ASSOC)) === false) {
            return false;
        }

        return $this->map($row);
    }

    public function getByEmailAndPassword(string $email, string $password): false|User
    {
        if (($statement = $this->connection->prepare('SELECT id, first_name, last_name, email, password, role, active FROM users WHERE email = :email AND password = :password')) === false) {
            return false;
        }

        if (!$statement->execute([':email' => $email, ':password' => $password])) {
            return false;
        }

        if (($row = $statement->fetch(PDO::FETCH_ASSOC)) === false) {
            return false;
        }

        return $this->map($row);
    }

    /**
     * Checks whether a user with the given ID exists.
     *
     * @param int $id The ID of the user to check.
     * @return bool Whether a user with the given ID exists.
     */
    public function existsById($id): bool
    {
        if (($statement = $this->connection->prepare('SELECT COUNT(*) FROM users WHERE id = :id')) === false) {
            return false;
        }

        if (!$statement->execute(['id' => $id])) {
            return false;
        }

        return boolval($statement->fetchColumn());
    }

    /**
     * Saves a user to the database
     *
     * @param User $entity The user to save.
     * @return false|User The saved user, or false if it couldn't be saved.
     */
    public function save($entity): false|User
    {
        if (
            ($statement = $this->connection->prepare(
                'INSERT INTO users ( id, first_name, last_name, email, password, role) 
                        VALUES ( :id, :first_name, :last_name, :email, :password, :role) 
                        ON DUPLICATE KEY UPDATE first_name = :first_name, last_name = :last_name, email = :email, password = :password, role = :role'
            )) === false
        ) {
            return false;
        }

        if ($statement->execute([
            'id' => $entity->getId(),
            'first_name' => $entity->getFirstName(),
            'last_name' => $entity->getLastName(),
            'email' => $entity->getEmail(),
            'password' => $entity->getPassword(),
            'role' => $entity->getRole()->value,
        ])) {
            return $entity;
        } else {
            return false;
        }
    }

    /**
     * Soft-Deletes a user from the database.
     *
     * @param int $id The ID of the user to delete.
     * @return bool Whether the user was deleted successfully.
     */
    public function deleteById($id): bool
    {
        if (($statement = $this->connection->prepare('UPDATE users SET active = false WHERE id = :id')) === false) {
            return false;
        }

        return $statement->execute(['id' => $id])  && $statement->rowCount() !== 0;
    }

    /**
     * Creates a user object from an array.
     *
     * @param array{ first_name: string, last_name: string, email: string, password: string, role: string, active: bool, id: int } $row
     * @return User The user object.
     */
    protected function map(array $row): User
    {
        return new User(
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['password'],
            $row['role'],
            filter_var($row['active'], FILTER_VALIDATE_BOOLEAN),
            (int) $row['id'],
        );
    }
}