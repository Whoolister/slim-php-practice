<?php

declare(strict_types=1);

namespace App\application\middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class JsonContentMiddleware implements Middleware
{
    /**
     * Sets the Content-Type header to application/json, only if it is not already set to something else.
     * This is meant as a shortcut to avoid having to set the header manually in every controller.
     *
     * @param Request $request The request object
     * @param RequestHandler $handler The next RequestHandler in the chain
     * @return Response Returns the response from the next RequestHandler
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        return empty($response->getHeader('Content-Type')) ?
            $response->withHeader('Content-Type', 'application/json') :
            $response;
    }
}