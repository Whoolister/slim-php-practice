<?php
declare(strict_types=1);

namespace App\application\middlewares\auth;

use App\application\services\JwtService;
use App\application\services\UserService;
use App\domain\exceptions\DomainException;
use App\domain\users\UserRole;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ConcreteResponse;
use function count;
use function explode;
use function var_dump;

class AuthenticationMiddleware implements Middleware
{
    private UserService $userService;
    private JwtService $jwtService;

    /**
     * Constructs a Security Middleware, which checks if the user is logged in, and refuses their requests if they are not
     *
     * @param UserService $userService The service to retrieve Users from
     */
    public function __construct(UserService $userService, JwtService $jwtService)
    {
        $this->userService = $userService;
        $this->jwtService = $jwtService;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $authHeader = explode(' ', $request->getHeaderLine('Authorization'), 2);

        if (count($authHeader) !== 2) {
            return (new ConcreteResponse())->withStatus(401, 'No Autorizado');
        }

        try {
            if ($this->jwtService->verifyToken($authHeader[1]) === false) {
                return (new ConcreteResponse())->withStatus(401, 'No Autorizado');
            }

            $payload = $this->jwtService->getPayload($authHeader[1]);

            $data = $payload->data;

            $request = $request->withAttribute(UserRole::class, UserRole::from($data->rol));
        } catch (Exception $e) {
            return (new ConcreteResponse())->withStatus(401, 'Error al decodificar el token');
        }

        return $handler->handle($request);
    }
}