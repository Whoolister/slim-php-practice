<?php

declare(strict_types=1);

namespace App\domain\users;

use App\domain\CrudRepository;
use PDO;
use function array_map;

/**
 * @implements CrudRepository<User, int>
 */
class UserRepository implements CrudRepository
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * @return int Number of users in the database.
     */
    public function count(): int
    {
        $statement = $this->connection->query('SELECT COUNT(*) FROM users');

        $statement->execute();

        return (int) $statement->fetchColumn();
    }

    /**
     * @return User[] All the users in the database.
     */
    public function getAll(): array
    {
        $statement = $this->connection->query('SELECT id, first_name, last_name, email, password, role, active FROM users');

        $statement->execute();

        $usersData = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($userData) => $this->map($userData), $usersData);
    }

    /**
     * Gets a user by its ID.
     *
     * @param int $id The ID of the user to get.
     * @return false|User The user with the given ID, or false if it doesn't exist.
     */
    public function getById($id): false|User
    {
        $statement = $this->connection->prepare(
            'SELECT id, first_name, last_name, email, password, role, active
                    FROM users
                    WHERE id = :id'
        );

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        $statement->execute();

        $accountData = $statement->fetch(PDO::FETCH_ASSOC);

        if ($accountData === false) {
            return false;
        }

        return $this->map($accountData);
    }

    /**
     * Gets a user by its email and password
     *
     * @param string $email Email of the user to get.
     * @param string $password Password of the user to get.
     * @return false|User The user with the given email and password, or false if it doesn't exist.
     */
    public function getByEmailAndPassword(string $email, string $password): false|User
    {
        $statement = $this->connection->prepare(
            'SELECT id, first_name, last_name, email, password, role, active 
                    FROM users 
                    WHERE email = :email AND password = :password'
        );

        $statement->bindValue(':email', $email);
        $statement->bindValue(':password', $password);

        $statement->execute();

        $userData = $statement->fetch(PDO::FETCH_ASSOC);

        if ($userData === false) {
            return false;
        }

        return $this->map($userData);
    }

    /**
     * Checks whether a user with the given ID exists.
     *
     * @param int $id The ID of the user to check.
     * @return bool Whether a user with the given ID exists.
     */
    public function existsById($id): bool
    {
        $statement = $this->connection->prepare('SELECT COUNT(*) FROM users WHERE id = :id');

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        $statement->execute();

        return (bool) $statement->fetchColumn();
    }

    /**
     * Saves a user to the database
     *
     * @param User $entity The user to save.
     * @return false|User The saved user, or false if it couldn't be saved.
     */
    public function save($entity): false|User
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users ( id, first_name, last_name, email, password, role) 
                    VALUES ( :id, :first_name, :last_name, :email, :password, :role) 
                    ON DUPLICATE KEY UPDATE first_name = :first_name, last_name = :last_name, email = :email, password = :password, role = :role'
        );

        $statement->bindValue(':id', $entity->id, PDO::PARAM_INT);
        $statement->bindValue(':first_name', $entity->firstName);
        $statement->bindValue(':last_name', $entity->lastName);
        $statement->bindValue(':email', $entity->email);
        $statement->bindValue(':password', $entity->password);
        $statement->bindValue(':role', $entity->role->value);

        if ($statement->execute() === false) {
            return false;
        }

        return $this->getById($entity->id ?? (int) $this->connection->lastInsertId());
    }

    /**
     * Soft-Deletes a user from the database.
     *
     * @param int $id The ID of the user to delete.
     * @return bool Whether the user was deleted successfully.
     */
    public function deleteById($id): bool
    {
        $statement = $this->connection->prepare('UPDATE users SET active = false WHERE id = :id');

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        return $statement->execute() && $statement->rowCount() !== 0;
    }

    protected function map(array $row): User
    {
        return new User(
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['password'],
            UserRole::from($row['role']),
            (bool) $row['active'],
            (int) $row['id'],
        );
    }
}