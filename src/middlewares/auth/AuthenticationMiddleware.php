<?php
declare(strict_types=1);

namespace App\middlewares\auth;

use App\entities\users\UserRole;
use App\services\UserService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ConcreteResponse;
use function count;
use function explode;

final readonly class AuthenticationMiddleware implements Middleware
{
    /**
     * Constructs a Security Middleware, which checks if the user is logged in, and refuses their requests if they are not
     *
     * @param UserService $userService The service to retrieve Users from
     */
    public function __construct(private UserService $userService)
    {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $authHeader = explode(' ', $request->getHeaderLine('Authorization'), 2);

        if (count($authHeader) !== 2) {
            return (new ConcreteResponse())->withStatus(401, 'No Autorizado');
        }

        list($type, $token) = $authHeader;

        if ($type !== 'Basic') {
            return (new ConcreteResponse())->withStatus(401, 'No Autorizado');
        }

        list($username, $password) = explode(':', base64_decode($token), 2);

        if (($user = $this->userService->getByEmailAndPassword($username, $password)) === false) {
            return (new ConcreteResponse())->withStatus(401, 'No Autorizado');
        }

        if (!$user->isActive()) {
            return (new ConcreteResponse())->withStatus(403, 'Prohibido');
        }

        return $handler->handle($request->withAttribute(UserRole::class, $user->getRole()));
    }
}