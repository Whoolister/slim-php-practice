<?php

declare(strict_types=1);

namespace App\application\controllers;

use App\application\services\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    private const ID_KEY = 'id';

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getAll(Request $request, Response $response): Response
    {
        $response->getBody()->write(json_encode(['usuarios' => $this->userService->getAll()]));

        return $response->withStatus(200, 'OK');
    }

    public function getById(Request $request, Response $response, $id): Response
    {
        $account = $this->userService->getById((int) $id);

        if ($account === false) {
            return $response->withStatus(404, 'No se encontró el usuario');
        }

        $response->getBody()->write(json_encode(['usuario' => $account]));

        return $response->withStatus(200, 'OK');
    }

    public function create(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        unset($body[self::ID_KEY]);

        $user = $this->userService->createUser($body);

        $user = $this->userService->save($user);

        $response->getBody()->write(json_encode(['usuario' => $user]));

        return $response->withStatus(201, 'Creado');
    }

    public function update(Request $request, Response $response, $id): Response
    {
        $body = $request->getParsedBody();
        $body[self::ID_KEY] = $id;

        $user = $this->userService->createUser($body);

        $user = $this->userService->save($user);

        $response->getBody()->write(json_encode(['usuario' => $user]));

        return $response->withStatus(200, 'OK');
    }

    public function delete(Request $request, Response $response, $id): Response
    {
        if ($this->userService->delete((int) $id) === false) {
            return $response->withStatus(500, 'No se pudo eliminar el usuario');
        }

        return $response->withStatus(200, 'OK');
    }
}