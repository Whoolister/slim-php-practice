<?php

declare(strict_types=1);

use App\application\controllers\LoginController;
use App\application\controllers\OrderController;
use App\application\controllers\OrderingController;
use App\application\controllers\OrderItemController;
use App\application\controllers\ProductController;
use App\application\controllers\SurveyController;
use App\application\controllers\TableController;
use App\application\controllers\UserController;
use App\application\middlewares\auth\AuthenticationMiddleware;
use App\application\middlewares\auth\AuthorizationMiddleware;
use App\application\middlewares\DomainExceptionMiddleware;
use App\application\middlewares\JsonContentMiddleware;
use Slim\App;
use Slim\Handlers\Strategies\RequestResponseArgs;
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

    /* Domain Exception Middleware */
    $app->add(DomainExceptionMiddleware::class);

    /* Set Default Invocation Strategy */
    $app->getRouteCollector()->setDefaultInvocationStrategy(new RequestResponseArgs());

    /* Routes */
    $app->post('/login', LoginController::class . ':login');

    $app->group('/usuarios', function (RouteCollectorProxy $group) {
        $group->get('[/]', UserController::class . ':getAll');
        $group->get('/{id}', UserController::class . ':getById');
        $group->post('[/]', UserController::class . ':create');
        $group->put('/{id}', UserController::class . ':update');
        $group->delete('/{id}', UserController::class . ':delete');
    })->add(AuthorizationMiddleware::class . ':asPartner')->add(AuthenticationMiddleware::class);

    $app->group('/productos', function (RouteCollectorProxy $group) {
        $group->get('[/]',ProductController::class . ':getAll');
        $group->get('/{id:[\d]+}',ProductController::class . ':getById');

        $group->post('[/]',ProductController::class . ':create')
            ->add(AuthorizationMiddleware::class . ':asKitchenStaff')->add(AuthenticationMiddleware::class);
        $group->put('/{id}',ProductController::class . ':update')
            ->add(AuthorizationMiddleware::class . ':asKitchenStaff')->add(AuthenticationMiddleware::class);
        $group->delete('/{id}',ProductController::class . ':delete')
            ->add(AuthorizationMiddleware::class . ':asKitchenStaff')->add(AuthenticationMiddleware::class);

        $group->get('/csv',ProductController::class . ':getAsCsv');
        $group->post('/csv',ProductController::class . ':addAsCsv')
            ->add(AuthorizationMiddleware::class . ':asKitchenStaff')->add(AuthenticationMiddleware::class);
    });

    $app->group('/mesas', function (RouteCollectorProxy $group) {
        $group->get('[/]',TableController::class . ':getAll');
        $group->get('/popular',TableController::class . ':getMostPopular');
        $group->get('/{id}',TableController::class . ':getById');

        $group->post('[/]',TableController::class . ':create')
            ->add(AuthorizationMiddleware::class . ':asPartner')->add(AuthenticationMiddleware::class);

        $group->post('/{id}/pedir',OrderingController::class . ':order')
            ->add(AuthorizationMiddleware::class . ':asWaiter')->add(AuthenticationMiddleware::class);
        $group->post('/{id}/foto',OrderingController::class . ':takePicture')
            ->add(AuthorizationMiddleware::class . ':asWaiter')->add(AuthenticationMiddleware::class);
        $group->post('/{id}/servir',OrderingController::class . ':serve')
            ->add(AuthorizationMiddleware::class . ':asWaiter')->add(AuthenticationMiddleware::class);
        $group->post('/{id}/cobrar',OrderingController::class . ':charge')
            ->add(AuthorizationMiddleware::class . ':asWaiter')->add(AuthenticationMiddleware::class);
        $group->post('/{id}/cerrar',OrderingController::class . ':close')
            ->add(AuthorizationMiddleware::class . ':asPartner')->add(AuthenticationMiddleware::class);

        $group->get('/{id}/pedidos/{orderId}', OrderingController::class . ':getPendingTime');
        $group->post('/{id}/pedidos/{orderId}/encuesta',SurveyController::class . ':create');

        $group->delete('/{id}',TableController::class . ':delete')
            ->add(AuthorizationMiddleware::class . ':asPartner')->add(AuthenticationMiddleware::class);
    });

    $app->group('/pedidos', function (RouteCollectorProxy $group) {
        $group->get('[/]',OrderController::class . ':getAll');

        $group->get('/pendientes',OrderingController::class . ':getAllPending');
        $group->get('/listos',OrderingController::class . ':getAllReady');

        $group->get('/{id}',OrderController::class . ':getById');
        $group->delete('/{id}',OrderController::class . ':delete');
    })->add(AuthorizationMiddleware::class . ':asWaiter')->add(AuthenticationMiddleware::class);

    $app->group('/items', function (RouteCollectorProxy $group) {
        $group->get('[/]',OrderItemController::class . ':getAll');
        $group->get('/pendientes',OrderItemController::class . ':getAllPending');
        $group->get('/{id}',OrderItemController::class . ':getById');

        $group->post('/{id}/preparar',OrderingController::class . ':startPreparation');
        $group->post('/{id}/terminar',OrderingController::class . ':finishPreparation');

        $group->delete('/{id}',OrderItemController::class . ':delete');
    })->add(AuthorizationMiddleware::class . ':asKitchenStaff')->add(AuthenticationMiddleware::class);

    $app->group('/encuestas', function (RouteCollectorProxy $group) {
        $group->get('[/]',SurveyController::class . ':getAll');
        $group->get('/{id}',SurveyController::class . ':getById');
        $group->post('[/]',SurveyController::class . ':create');
        $group->put('/{id}',SurveyController::class . ':update');
        $group->delete('/{id}',SurveyController::class . ':delete')
            ->add(AuthorizationMiddleware::class . ':asPartner')->add(AuthenticationMiddleware::class);
    });
};
