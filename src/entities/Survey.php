<?php

declare(strict_types=1);

namespace App\entities;

use JsonSerializable;

readonly class Survey implements JsonSerializable
{
    /**
     * Constructs a Survey
     *
     * @param int $orderId ID of the Order that this survey belongs to.
     * @param int $tableRating Rating of the table.
     * @param int $restaurantRating Rating of the restaurant.
     * @param int $waiterRating Rating of the waiter.
     * @param int $chefRating Rating of the chef.
     * @param string $comment Comment of the Survey, defaults to an empty string.
     * @param int $id ID of the Survey, defaults to 0.
     */
    public function __construct(
        public int $orderId,
        public int $tableRating,
        public int $restaurantRating,
        public int $waiterRating,
        public int $chefRating,
        public string $comment = '',
        public int $id = 0
    )
    {
    }

    /** @noinspection SpellCheckingInspection
     * @noinspection SpellCheckingInspection
     * @noinspection SpellCheckingInspection
     * @noinspection SpellCheckingInspection
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'idPedido' => $this->orderId,
            'puntuacionMesa' => $this->tableRating,
            'puntuacionRestaurante' => $this->restaurantRating,
            'puntuacionCamarero' => $this->waiterRating,
            'puntuacionCocinero' => $this->chefRating,
            'comentario' => $this->comment,
        ];
    }
}