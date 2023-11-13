<?php

declare(strict_types=1);

use App\repositories\orders\OrderRepository;
use App\repositories\orders\SurveyRepository;
use App\repositories\products\ProductRepository;
use App\repositories\tables\TableRepository;
use App\repositories\users\UserRepository;
use DI\ContainerBuilder;
use function DI\autowire;

/**
 * Sets the repositories and database connections to the DI Container
 *
 * @param ContainerBuilder $containerBuilder The DI Container
 */
return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        PDO::class => function () {
            $connection = new PDO(
                dsn: "mysql:host=$_ENV[DB_HOST];dbname=$_ENV[DB_NAME]",
                username: $_ENV['DB_USERNAME'],
                password: $_ENV['DB_PASSWORD'] ?? null
            );
            $connection->setAttribute(PDO::ATTR_ERRMODE, $_ENV['DEVELOPMENT_MODE'] ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_SILENT);
            return $connection;
        },
        OrderRepository::class => autowire(),
        ProductRepository::class => autowire(),
        SurveyRepository::class => autowire(),
        TableRepository::class => autowire(),
        UserRepository::class => autowire(),
    ]);
};