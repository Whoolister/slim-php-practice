<?php

declare(strict_types=1);

use App\repositories\OrderRepository;
use App\repositories\ProductRepository;
use App\repositories\SurveyRepository;
use App\repositories\TableRepository;
use App\repositories\UserRepository;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

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
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connection;
        },
        OrderRepository::class => fn (ContainerInterface $container) => new OrderRepository($container->get(PDO::class)),
        ProductRepository::class => fn (ContainerInterface $container) => new ProductRepository($container->get(PDO::class)),
        SurveyRepository::class => fn (ContainerInterface $container) => new SurveyRepository($container->get(PDO::class)),
        TableRepository::class => fn (ContainerInterface $container) => new TableRepository($container->get(PDO::class)),
        UserRepository::class => fn (ContainerInterface $container) => new UserRepository($container->get(PDO::class)),
    ]);
};