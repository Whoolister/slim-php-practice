<?php

declare(strict_types=1);

namespace App\application\controllers;

use App\application\services\JwtService;
use App\application\services\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginController
{
    private const EMAIL_KEY = 'email';
    private const PASSWORD_KEY = 'password';

    private UserService $userService;
    private JwtService $jwtService;

    public function __construct(UserService $userService, JwtService $jwtService)
    {
        $this->userService = $userService;
        $this->jwtService = $jwtService;
    }

    public function login(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        if (!isset($body[self::EMAIL_KEY])) {
            return $response->withStatus(400, 'Faltan el email');
        }

        if (!isset($body[self::PASSWORD_KEY])) {
            return $response->withStatus(400, 'Falta la contraseña');
        }

        $email = $body[self::EMAIL_KEY];
        $password = $body[self::PASSWORD_KEY];

        $user = $this->userService->getByEmailAndPassword($email, $password);

        if ($user === false) {
            return $response->withStatus(401, 'Credenciales inválidas');
        }

        $jwt = $this->jwtService->createToken($user->jsonSerialize());

        $response->getBody()->write(json_encode(['token' => $jwt]));

        return $response->withStatus(200, 'OK');
    }
}