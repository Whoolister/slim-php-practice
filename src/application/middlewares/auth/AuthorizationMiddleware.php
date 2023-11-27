<?php
declare(strict_types=1);

namespace App\application\middlewares\auth;

use App\domain\users\UserRole;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ConcreteResponse;
use function in_array;

class AuthorizationMiddleware
{
    public function asPartner(Request $request, RequestHandler $handler): Response
    {
        if (!self::validateRole($request->getAttribute(UserRole::class), UserRole::PARTNER)) {
            return (new ConcreteResponse())->withStatus(403, 'Prohibido');
        }

        return $handler->handle($request);
    }

    public function asWaiter(Request $request, RequestHandler $handler): Response
    {
        if (!self::validateRole($request->getAttribute(UserRole::class), UserRole::PARTNER, UserRole::WAITER)) {
            return (new ConcreteResponse())->withStatus(403, 'Prohibido');
        }

        return $handler->handle($request);
    }

    public function asKitchenStaff(Request $request, RequestHandler $handler): Response
    {
        if (!self::validateRole(
            $request->getAttribute(UserRole::class),
            UserRole::PARTNER,
            UserRole::BARTENDER,
            UserRole::BREWER,
            UserRole::CHEF,
            UserRole::BAKER,
        )) {
            return (new ConcreteResponse())->withStatus(403, 'Prohibido');
        }

        return $handler->handle($request);
    }

    private static function validateRole(?UserRole $role, UserRole ...$allowedRoles): bool
    {
        return in_array($role, $allowedRoles);
    }
}