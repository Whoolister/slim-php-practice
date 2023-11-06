<?php

declare(strict_types=1);

namespace App\controllers;

use App\entities\users\UserRole;
use App\services\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotImplementedException;
use const FILTER_NULL_ON_FAILURE;

readonly class UserController
{
    public function __construct(private UserService $userService)
    {
    }

    public function GetAll(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode($this->userService->GetAll()));

        return $response->withStatus(200, 'OK');
    }

    public function GetOne(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        if (($id = $args['id']) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (!is_numeric($id)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        if (($user = $this->userService->GetById((int) $id)) === false) {
            return $response->withStatus(404, 'No se encontró el usuario');
        }

        $response->getBody()->write(json_encode($user));

        return $response->withStatus(200, 'OK');
    }

    public function Add(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        $body = $request->getParsedBody();

        if (($firstName = $body['nombre']) === null) {
            return $response->withStatus(400, 'Falta el nombre');
        }

        if (($lastName = $body['apellido']) === null) {
            return $response->withStatus(400, 'Falta el apellido');
        }

        if (($email = $body['email']) === null) {
            return $response->withStatus(400, 'Falta el email');
        }

        if (($password = $body['password']) === null) {
            return $response->withStatus(400, 'Falta la contraseña');
        }

        if (($role = $body['rol']) === null) {
            return $response->withStatus(400, 'Falta el rol');
        }

        if (($role = UserRole::tryFrom($role)) === null) {
            return $response->withStatus(400, 'Rol inválido');
        }

        if (($user = $this->userService->Add($firstName, $lastName, $email, $password, $role)) === false) {
            return $response->withStatus(500, 'No se pudo agregar el usuario');
        }

        $response->getBody()->write(json_encode($user));

        return $response->withStatus(201, 'Creado');
    }

    public function Update(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        if (($id = $args['id']) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (!is_numeric($id)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        $body = $request->getParsedBody();

        if (($firstName = $body['nombre']) === null) {
            return $response->withStatus(400, 'Falta el nombre');
        }

        if (($lastName = $body['apellido']) === null) {
            return $response->withStatus(400, 'Falta el apellido');
        }

        if (($email = $body['email']) === null) {
            return $response->withStatus(400, 'Falta el email');
        }

        if (($password = $body['password']) === null) {
            return $response->withStatus(400, 'Falta la contraseña');
        }

        if (($role = $body['rol']) === null) {
            return $response->withStatus(400, 'Falta el rol');
        }

        if (($role = UserRole::tryFrom($role)) === null) {
            return $response->withStatus(400, 'Rol inválido');
        }

        if (($active = $body['activo']) === null) {
            return $response->withStatus(400, 'Falta el estado');
        }

        if (($active = filter_var($active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) === null) {
            return $response->withStatus(400, 'Estado inválido');
        }

        if (($user = $this->userService->Update((int) $id, $firstName, $lastName, $email, $password, $role, $active)) === false) {
            return $response->withStatus(500, 'No se pudo actualizar el usuario');
        }

        $response->getBody()->write(json_encode($user));

        return $response->withStatus(200, 'OK');
    }

    public function Delete(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        if (($id = $args['id']) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (!is_numeric($id)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        if (($user = $this->userService->Delete((int) $id)) === false) {
            return $response->withStatus(500, 'No se pudo eliminar el usuario');
        }

        $response->getBody()->write(json_encode($user));

        return $response->withStatus(200, 'OK');
    }
}