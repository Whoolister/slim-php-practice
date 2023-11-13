<?php

declare(strict_types=1);

namespace App\entities\users;

use App\entities\Entity;
use function is_string;

final class User extends Entity
{
    private string $firstName;
    private string $lastName;
    private string $email;
    private string $password;
    private UserRole $role;
    private bool $active;

    /**
     * @return string First name of the User.
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName First name of the User.
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string Last name of the User.
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName Last name of the User.
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string Email of the User.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email Email of the User.
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string Password of the User.
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return UserRole Role of the User.
     */
    public function getRole(): UserRole
    {
        return $this->role;
    }

    /**
     * @param string|UserRole $role Role of the User.
     */
    public function setRole(string|UserRole $role): void
    {
        $this->role = is_string($role) ? UserRole::from($role) : $role;
    }

    /**
     * @return bool Whether the User is active or not.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active Whether the User is active or not.
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return ?int ID of the User.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param ?int $id ID of the User.
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Constructs a User.
     *
     * @param string $firstName First name of the User.
     * @param string $lastName Last name of the User.
     * @param string $email Email of the User.
     * @param string $password Password of the User.
     * @param string|UserRole $role Role of the User.
     * @param bool $active Whether the User is active or not, defaults to true.
     * @param ?int $id ID of the User, defaults to null.
     */
    public function __construct(string $firstName, string $lastName, string $email, string $password, string|UserRole $role, bool $active = true, ?int $id = null)
    {
        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setRole($role);
        $this->setActive($active);
        $this->setId($id);
    }

    /**
     * @return array{ nombre: string, apellido: string, email: string, rol: string, activo: bool, id: ?int } Array representation of the User.
     */
    public function jsonSerialize(): array
    {
        return [
            'nombre' => $this->getFirstName(),
            'apellido' => $this->getLastName(),
            'email' => $this->getEmail(),
            'rol' => $this->getRole()->value,
            'activo' => $this->isActive(),
            'id' => $this->getId(),
        ];
    }
}
