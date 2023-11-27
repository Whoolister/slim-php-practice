<?php
declare(strict_types=1);

namespace App\application\middlewares;

use App\domain\exceptions\DomainException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ConcreteResponse;

class DomainExceptionMiddleware implements Middleware
{
    /**
     * Catches any DomainException thrown by the RequestHandler and returns a response with the exception's message and code.
     *
     * @param Request $request The request object
     * @param RequestHandler $handler The next RequestHandler in the chain
     * @return Response Returns the response from the next RequestHandler
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        try {
            return $handler->handle($request);
        } catch (DomainException $e) {
            return (new ConcreteResponse())->withStatus($e->getCode(), $e->getMessage());
        }
    }
}