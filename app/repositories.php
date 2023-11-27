<?php

declare(strict_types=1);

use DI\ContainerBuilder;

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

            $connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            return $connection;
        },
    ]);
};