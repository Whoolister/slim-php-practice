<?php

declare(strict_types=1);

use Dotenv\Dotenv;

/**
 * Asserts that all necessary environment variables are set and not empty
 *
 * @param Dotenv $env The Dotenv instance to use
 */
return function (Dotenv $env) {
    $env->load();
    $env->required(['DB_HOST', 'DB_NAME', 'DB_USERNAME'])->notEmpty();
    $env->ifPresent('DB_PASSWORD')->notEmpty();
};