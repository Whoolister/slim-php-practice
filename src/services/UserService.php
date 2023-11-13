<?php

declare(strict_types=1);

namespace App\services;

use App\entities\users\User;
use App\entities\users\UserRole;
use App\repositories\users\UserRepository;

final readonly class UserService
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
    public function getAll(): array
    {
        return $this->userRepository->getAll();
    }

    /**
     * Gets a user by its ID.
     *
     * @param int $id The ID of the user to get.
     * @return false|User The user with the given ID, or false if it doesn't exist.
     */
    public function getOne(int $id): false|User
    {
        return $this->userRepository->getById($id);
    }

    public function getByEmailAndPassword(string $email, string $password): false|User
    {
        return $this->userRepository->getByEmailAndPassword($email, $password);
    }

    /**
     * Persists a user.
     *
     * @param User $user The user to persist.
     * @return false|User The created user, or false if it couldn't be created.
     */
    public function add(User $user): false|User
    {
        if ($user->getId() !== null) {
            return false;
        }

        return $this->userRepository->save($user);
    }

    /**
     * Updates a user.
     *
     * @param User $user The user to update.
     * @return false|User The updated user, or false if it couldn't be updated.
     */
    public function update(User $user): false|User
    {
        if (!$this->userRepository->existsById($user->getId())) {
            return false;
        }

        return $this->userRepository->save($user);
    }

    /**
     * Deletes a user, unless it is a partner.
     *
     * @param int $id The ID of the user to delete.
     * @return bool Whether the user was deleted successfully.
     */
    public function delete(int $id): bool
    {
        if (($user = $this->userRepository->getById($id)) !== false && $user->getRole() === UserRole::PARTNER) {
            return false;
        }

        return $this->userRepository->deleteById($id);
    }
}