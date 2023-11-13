<?php

declare(strict_types=1);

namespace App\entities;

use JsonSerializable;

/**
 * Abstract class that represents an Entity.
 */
abstract class Entity implements JsonSerializable
{
    /**
     * @var null|int|string The ID of the Entity, or null if not set.
     */
    protected null|int|string $id;
}