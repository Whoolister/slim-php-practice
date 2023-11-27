<?php

declare(strict_types=1);

namespace App\domain\users;

use App\domain\Entity;

class User extends Entity
{
    /**
     * Constructs a User.
     *
     * @param string $firstName First name of the User.
     * @param string $lastName Last name of the User.
     * @param string $email Email of the User.
     * @param string $password Password of the User.
     * @param UserRole $role Role of the User.
     * @param bool $active Whether the User is active or not, defaults to true.
     * @param ?int $id ID of the User, defaults to null.
     */
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $password,
        public UserRole $role,
        public bool $active = true,
        ?int $id = null,
    ) {
        parent::__construct($id);
    }

    /**
     * @inheritdoc Entity::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        return [
            'nombre' => $this->firstName,
            'apellido' => $this->lastName,
            'email' => $this->email,
            'rol' => $this->role->value,
            'activo' => $this->active,
            'id' => $this->id,
        ];
    }
}
