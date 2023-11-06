<?php

declare(strict_types=1);

namespace App\services;

use App\entities\users\User;
use App\entities\users\UserRole;
use App\repositories\UserRepository;

readonly class UserService
{
    /**
     * Constructs a User Service.
     *
     * @param UserRepository $userRepository The user repository to use.
     */
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * @return User[] All the products currently persisted.
     */
    public function GetAll(): array
    {
        return $this->userRepository->GetAll();
    }

    /**
     * Gets a user by its ID.
     *
     * @param int $id The ID of the user to get.
     * @return false|User The user with the given ID, or false if it doesn't exist.
     */
    public function GetById(int $id): false|User
    {
        return $this->userRepository->GetById($id);
    }

    /**
     * Persists a user.
     *
     * @param string $firstName The first name of the user.
     * @param string $lastName The last name of the user.
     * @param string $email The email of the user.
     * @param string $password The password of the user.
     * @param UserRole $role The role of the user.
     * @return false|User The created user, or false if it couldn't be created.
     */
    public function Add(string $firstName, string $lastName, string $email, string $password, UserRole $role): false|User
    {
        $user = new User($firstName, $lastName, $email, $password, $role);

        return $this->userRepository->Add($user) ? $user : false;
    }

    /**
     * Updates a user.
     *
     * @param int $id The ID of the user to update.
     * @param string $firstName The first name of the user.
     * @param string $lastName The last name of the user.
     * @param string $email The email of the user.
     * @param string $password The password of the user.
     * @param UserRole $role The role of the user.
     * @param bool $active Whether the user is active.
     * @return false|User The updated user, or false if it couldn't be updated.
     */
    public function Update(int $id, string $firstName, string $lastName, string $email, string $password, UserRole $role, bool $active): false| User
    {
        $user = new User($firstName, $lastName, $email, $password, $role, $active, $id);

        return $this->userRepository->Update($user) ? $user : false;
    }

    /**
     * Deletes a user.
     *
     * @param int $id The ID of the user to delete.
     * @return bool Whether the user was deleted successfully.
     */
    public function Delete(int $id): bool
    {
        return $this->userRepository->Delete($id);
    }
}