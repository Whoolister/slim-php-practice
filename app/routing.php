<?php

declare(strict_types=1);

use App\controllers\ProductController;
use App\controllers\SurveyController;
use App\controllers\TableController;
use App\controllers\UserController;
use App\controllers\OrderController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/**
 * Sets the front-controller routes to the appropriate Controllers
 *
 * @param App $app The Slim App object
 */
return function(App $app)
{
    $app->group('/usuarios', function (RouteCollectorProxy $group) {
        $group->get('[/]', UserController::class . ':GetAll');
        $group->get('/{id}', UserController::class . ':GetOne');
        $group->post('[/]', UserController::class . ':Add');
        $group->put('/{id}', UserController::class . ':Update');
        $group->delete('/{id}', UserController::class . ':Delete');
    });

    $app->group('/productos', function (RouteCollectorProxy $group){
        $group->get('[/]',ProductController::class . ':GetAll');
        $group->get('/{id}',ProductController::class . ':GetOne');
        $group->post('[/]',ProductController::class . ':Add');
        $group->put('/{id}',ProductController::class . ':Update');
        $group->delete('/{id}',ProductController::class . ':Delete');
    });

    $app->group('/mesas', function (RouteCollectorProxy $group){
        $group->get('[/]',TableController::class . ':GetAll');
        $group->get('/{id}',TableController::class . ':GetOne');
        $group->post('[/]',TableController::class . ':Add');
        $group->put('/{id}',TableController::class . ':Update');
        $group->delete('/{id}',TableController::class . ':Delete');
    });

    $app->group('/pedidos', function (RouteCollectorProxy $group){
        $group->get('[/]',OrderController::class . ':GetAll');
        $group->get('/{id}',OrderController::class . ':GetOne');
        $group->post('[/]',OrderController::class . ':Add');
        $group->put('/{id}',OrderController::class . ':Update');
        $group->delete('/{id}',OrderController::class . ':Delete');
    });

    $app->group('/encuestas', function (RouteCollectorProxy $group){
        $group->get('[/]',SurveyController::class . ':GetAll');
        $group->get('/{id}',SurveyController::class . ':GetOne');
        $group->post('[/]',SurveyController::class . ':Add');
        $group->put('/{id}',SurveyController::class . ':Update');
        $group->delete('/{id}',SurveyController::class . ':Delete');
    });

    $app->get('[/]', function (Request $request, Response $response) {
        $response = $response->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode(['msg' => 'Bienvenido a La Parissiene!']));
        
        return $response->withStatus(200, 'OK');
    });
};