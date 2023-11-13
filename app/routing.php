<?php

declare(strict_types=1);

use App\controllers\OrderController;
use App\controllers\OrderItemController;
use App\controllers\ProductController;
use App\controllers\SurveyController;
use App\controllers\TableController;
use App\controllers\UserController;
use App\entities\users\UserRole;
use App\middlewares\auth\AuthenticationMiddleware;
use App\middlewares\auth\AuthorizationMiddleware;
use App\middlewares\JsonContentMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/**
 * Sets the front-controller routes to the appropriate Controllers
 *
 * @param App $app The Slim App object
 */
return function(App $app): void
{
    /* Content-Type Middleware */
    $app->add(JsonContentMiddleware::class);

    /* Authentication Middleware */
    $app->add(AuthenticationMiddleware::class);

    /* Authorization Middleware */
    $authorizeWaiters = new AuthorizationMiddleware([UserRole::WAITER]);

    /* Routing Groups */
    $surveyRouting = function (RouteCollectorProxy $group) {
        $group->get('[/]',SurveyController::class . ':getAll');
        $group->get('/{idEncuesta:[\d]+}',SurveyController::class . ':getOne');
        $group->post('[/]',SurveyController::class . ':add');
        $group->put('/{idEncuesta:[\d]+}',SurveyController::class . ':update');
        $group->delete('/{idEncuesta:[\d]+}',SurveyController::class . ':delete');
    };

    $orderItemRouting = function (RouteCollectorProxy $group) {
        $group->get('[/]',OrderItemController::class . ':getAll');
        $group->get('/{idItemPedido:[\d]+}',OrderItemController::class . ':getOne');
        $group->post('[/]',OrderItemController::class . ':add');
        $group->put('/{idItemPedido:[\d]+}',OrderItemController::class . ':update');
        $group->delete('/{idItemPedido:[\d]+}',OrderItemController::class . ':delete');
    };

    $orderRouting = function (RouteCollectorProxy $group) use ($orderItemRouting, $surveyRouting) {
        $group->get('[/]',OrderController::class . ':getAll');
        $group->get('/{idPedido:[\d]+}',OrderController::class . ':getOne');
        $group->post('[/]',OrderController::class . ':add');
        $group->put('/{idPedido:[\d]+}',OrderController::class . ':update');
        $group->delete('/{idPedido:[\d]+}',OrderController::class . ':delete');

        $group->group('/{idPedido:[\d]+}/encuestas', $surveyRouting);

        $group->group('/{idPedido:[\d]+}/items', $orderItemRouting);
    };

    /* Routes */
    $app->group('/usuarios', function (RouteCollectorProxy $group) {
        $group->get('[/]', UserController::class . ':getAll');
        $group->get('/{idUsuario:[\d]+}', UserController::class . ':getOne');
        $group->post('[/]', UserController::class . ':add');
        $group->put('/{idUsuario:[\d]+}', UserController::class . ':update');
        $group->delete('/{idUsuario:[\d]+}', UserController::class . ':delete');
    })->add(AuthorizationMiddleware::class);

    $app->group('/productos', function (RouteCollectorProxy $group) {
        $group->get('[/]',ProductController::class . ':getAll');
        $group->get('/{idProducto:[\d]+}',ProductController::class . ':getOne');
        $group->post('[/]',ProductController::class . ':add');
        $group->put('/{idProducto:[\d]+}',ProductController::class . ':update');
        $group->delete('/{idProducto:[\d]+}',ProductController::class . ':delete');
    });

    $app->group('/mesas', function (RouteCollectorProxy $group) use ($authorizeWaiters, $orderRouting) {
        $group->get('[/]',TableController::class . ':getAll');
        $group->get('/{idMesa:[\d\w]+}',TableController::class . ':getOne');
        $group->post('[/]',TableController::class . ':add')
            ->add(AuthorizationMiddleware::class);
        $group->put('/{idMesa:[\d\w]+}',TableController::class . ':update')
            ->addMiddleware($authorizeWaiters);
        $group->delete('/{idMesa:[\d\w]+}',TableController::class . ':delete')
            ->add(AuthorizationMiddleware::class);

        $group->group('/{idMesa:[\d\w]+}/pedidos', $orderRouting)->addMiddleware($authorizeWaiters);
    });

    $app->group('/pedidos', $orderRouting)->addMiddleware($authorizeWaiters);

    $app->group('/items', $orderItemRouting);

    $app->group('/encuestas', $surveyRouting);


    $app->get('[/]', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['msg' => 'Bienvenido a La Comanda!']));

        return $response->withStatus(200, 'OK');
    });
};
