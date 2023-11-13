<?php
declare(strict_types=1);

namespace App\middlewares\auth;

use App\entities\users\UserRole;
use App\services\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ConcreteResponse;
use function count;
use function explode;
use function in_array;

final readonly class AuthorizationMiddleware implements Middleware
{
    /**
     * Constructs a Security Middleware, which checks if the User is authorized to access the resource.
     *
     * @param UserRole[] $allowedRoles The allowed roles to access the resource
     */
    public function __construct(private array $allowedRoles = [UserRole::PARTNER])
    {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $role = $request->getAttribute(UserRole::class);

        if (!in_array($role, $this->allowedRoles)) {
            return (new ConcreteResponse())->withStatus(403, 'Prohibido}');
        }

        return $handler->handle($request);
    }
}