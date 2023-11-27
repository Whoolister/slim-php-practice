<?php

declare(strict_types=1);

// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Environment Variables
$environment = require __DIR__ . '/../app/environment.php';
$environment(Dotenv::createImmutable(__DIR__ . '/../'));

// Instantiate Dependency Injection Container
$containerBuilder = new ContainerBuilder();

// Set up repositories
$repositories = require __DIR__ . '/../app/repositories.php';
$repositories($containerBuilder);

$container = $containerBuilder->build();

// Instantiate app
AppFactory::setContainer($container);
$app = AppFactory::create();

// Add routes
$routing = require __DIR__ . '/../app/routing.php';
$routing($app);

// Add body parser
$app->addBodyParsingMiddleware();

// Set default Timezone
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Add error middleware
$app->addErrorMiddleware(
    displayErrorDetails: true,
    logErrors: true,
    logErrorDetails: true
);

$app->run();
