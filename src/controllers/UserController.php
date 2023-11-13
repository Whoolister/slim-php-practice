<?php

declare(strict_types=1);

namespace App\controllers;

use App\entities\users\User;
use App\entities\users\UserRole;
use App\services\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final readonly class UserController
{
    private const ID_KEY = 'idUsuario';
    private const FIRST_NAME_KEY = 'nombre';
    private const LAST_NAME_KEY = 'apellido';
    private const EMAIL_KEY = 'email';
    private const PASSWORD_KEY = 'password';
    private const ROLE_KEY = 'rol';

    public function __construct(private UserService $userService)
    {
    }

    public function getAll(Request $request, Response $response, array $args): Response
    {
        $response->getBody()->write(json_encode($this->userService->getAll()));

        return $response->withStatus(200, 'OK');
    }

    public function GetOne(Request $request, Response $response, array $args): Response
    {
        if (!isset($args[self::ID_KEY])) return $response->withStatus(400, 'Falta el ID');

        $id = $args[self::ID_KEY];

        if (!is_numeric($id)) return $response->withStatus(400, 'El ID debe ser un número');

        if (($user = $this->userService->getOne((int) $id)) === false) {
            return $response->withStatus(404, 'No se encontró el usuario');
        }

        $response->getBody()->write(json_encode($user));

        return $response->withStatus(200, 'OK');
    }

    public function add(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();

        if (!isset($body[self::FIRST_NAME_KEY])) return $response->withStatus(400, 'Falta el nombre');
        if (!isset($body[self::LAST_NAME_KEY])) return $response->withStatus(400, 'Falta el apellido');
        if (!isset($body[self::EMAIL_KEY])) return $response->withStatus(400, 'Falta el email');
        if (!isset($body[self::PASSWORD_KEY])) return $response->withStatus(400, 'Falta la contraseña');
        if (!isset($body[self::ROLE_KEY])) return $response->withStatus(400, 'Falta el rol');

        $firstName = $body[self::FIRST_NAME_KEY];
        $lastName = $body[self::LAST_NAME_KEY];
        $email = $body[self::EMAIL_KEY];
        $password = $body[self::PASSWORD_KEY];
        $role = UserRole::tryFrom($body[self::ROLE_KEY]);

        if ($role === null) return $response->withStatus(400, 'Rol inválido');

        if (($user = $this->userService->add(new User($firstName, $lastName, $email, $password, $role))) === false) {
            return $response->withStatus(500, 'No se pudo agregar el usuario');
        }

        $response->getBody()->write(json_encode($user));

        return $response->withStatus(201, 'Creado');
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        if (!isset($args[self::ID_KEY])) return $response->withStatus(400, 'Falta el ID');

        $id = $args[self::ID_KEY];

        if (!is_numeric($id)) return $response->withStatus(400, 'El ID debe ser un número');

        $body = $request->getParsedBody();

        if (!isset($body[self::FIRST_NAME_KEY])) return $response->withStatus(400, 'Falta el nombre');
        if (!isset($body[self::LAST_NAME_KEY])) return $response->withStatus(400, 'Falta el apellido');
        if (!isset($body[self::EMAIL_KEY])) return $response->withStatus(400, 'Falta el email');
        if (!isset($body[self::PASSWORD_KEY])) return $response->withStatus(400, 'Falta la contraseña');
        if (!isset($body[self::ROLE_KEY])) return $response->withStatus(400, 'Falta el rol');

        $firstName = $body[self::FIRST_NAME_KEY];
        $lastName = $body[self::LAST_NAME_KEY];
        $email = $body[self::EMAIL_KEY];
        $password = $body[self::PASSWORD_KEY];
        $role = UserRole::tryFrom($body[self::ROLE_KEY]);

        if ($role === null) return $response->withStatus(400, 'Rol inválido');

        if (($user = $this->userService->update(new User($firstName, $lastName, $email, $password, $role, id: (int) $id))) === false) {
            return $response->withStatus(500, 'No se pudo actualizar el usuario');
        }

        $response->getBody()->write(json_encode($user));

        return $response->withStatus(200, 'OK');
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        if (!isset($args[self::ID_KEY])) return $response->withStatus(400, 'Falta el ID');

        $id = $args[self::ID_KEY];

        if (!is_numeric($id)) return $response->withStatus(400, 'El ID debe ser un número');

        if (($user = $this->userService->delete((int) $id)) === false) {
            return $response->withStatus(500, 'No se pudo eliminar el usuario');
        }

        $response->getBody()->write(json_encode($user));

        return $response->withStatus(200, 'OK');
    }
}