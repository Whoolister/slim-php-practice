<?php

declare(strict_types=1);

namespace App\application\services;

use App\domain\exceptions\DomainException;
use App\domain\users\User;
use App\domain\users\UserRepository;
use App\domain\users\UserRole;
use function filter_var;
use const FILTER_VALIDATE_EMAIL;

class UserService
{
    private const ID_KEY = 'id';
    private const FIRST_NAME_KEY = 'nombre';
    private const LAST_NAME_KEY = 'apellido';
    private const EMAIL_KEY = 'email';
    private const PASSWORD_KEY = 'password';
    private const ROLE_KEY = 'rol';

    private UserRepository $userRepository;

    /**
     * Constructs a User Service.
     *
     * @param UserRepository $userRepository The user repository to use.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
    public function getById(int $id): false|User
    {
        return $this->userRepository->getById($id);
    }

    /**
     * Gets a user by its email and password
     *
     * @param string $email The email of the user to get.
     * @param string $password The password of the user to get.
     * @return false|User The user with the given email and password, or false if it doesn't exist.
     */
    public function getByEmailAndPassword(string $email, string $password): false|User
    {
        return $this->userRepository->getByEmailAndPassword($email, $password);
    }

    /**
     * Saves a user.
     *
     * @param User $user The user to save.
     * @return User The saved user.
     * @throws DomainException If the user could not be saved.
     */
    public function save(User $user): User
    {
        if (($result = $this->userRepository->save($user)) === false) {
            throw new DomainException('No se pudo guardar el usuario', 500);
        }

        return $result;
    }

    /**
     * Deletes a user, unless it is a partner.
     *
     * @param int $id The ID of the user to delete.
     * @return bool Whether the user was deleted successfully.
     * @throws DomainException If the user does not exist.
     */
    public function delete(int $id): bool
    {
        if ($this->userRepository->existsById($id) === false) {
            throw new DomainException('El usuario no existe', 404);
        }

        return $this->userRepository->deleteById($id);
    }

    /**
     * Creates a user from the given data.
     *
     * @param mixed $userData The data to create a user from.
     * @return User The created user.
     * @throws DomainException If the data is invalid.
     */
    public function createUser(mixed $userData): User
    {
        if ($userData === null) {
            throw new DomainException('Los datos del usuario no pueden estar vacios.', 400);
        }

        if (!isset($userData[self::FIRST_NAME_KEY])) {
            throw new DomainException('Falta el nombre', 400);
        }

        if (!isset($userData[self::LAST_NAME_KEY])) {
            throw new DomainException('Falta el apellido', 400);
        }

        if (!isset($userData[self::EMAIL_KEY])) {
            throw new DomainException('Falta el email', 400);
        }

        if (!isset($userData[self::PASSWORD_KEY])) {
            throw new DomainException('Falta la contraseña', 400);
        }

        if (!isset($userData[self::ROLE_KEY])) {
            throw new DomainException('Falta el rol', 400);
        }

        $id = $userData[self::ID_KEY] ?? null;
        $firstName = $userData[self::FIRST_NAME_KEY];
        $lastName = $userData[self::LAST_NAME_KEY];
        $email = $userData[self::EMAIL_KEY];
        $password = $userData[self::PASSWORD_KEY];
        $role = UserRole::tryFromIgnoringCase($userData[self::ROLE_KEY]);

        if ($id !== null && !is_numeric($id)) {
            throw new DomainException('El id debe ser un numero', 400);
        }

        if (empty($firstName)) {
            throw new DomainException('El nombre no puede estar vacio', 400);
        }

        if (empty($lastName)) {
            throw new DomainException('El apellido no puede estar vacio', 400);
        }

        if (empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new DomainException('El email no es valido', 400);
        }

        if (empty($password)) {
            throw new DomainException('La contraseña no puede estar vacia', 400);
        }

        if ($role === null) {
            throw new DomainException('El rol no es válido', 400);
        }

        return new User(
            firstName: $firstName,
            lastName: $lastName,
            email: $email,
            password: $password,
            role: $role,
            id: $id === null ? null : (int) $id
        );
    }
}