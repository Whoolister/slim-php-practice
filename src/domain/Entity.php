<?php

declare(strict_types=1);

namespace App\domain;

use JsonSerializable;

/**
 * Abstract class that represents an Entity.
 * @template ID of int|string The type of ID that the Entity uses.
 */
abstract class Entity implements JsonSerializable
{
    /**
     * Creates a new Entity.
     *
     * @param ?ID $id The ID of the Entity, or empty if not set.
     */
    protected function __construct(public $id)
    {
    }
}