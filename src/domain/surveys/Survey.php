<?php

declare(strict_types=1);

namespace App\domain\surveys;

use App\domain\Entity;

/**
 * @template-extends Entity<int>
 */
class Survey extends Entity
{
    /**
     * Constructs a Survey
     *
     * @param string $orderId ID of the Order that this survey belongs to.
     * @param int $tableRating Rating of the table.
     * @param int $restaurantRating Rating of the restaurant.
     * @param int $waiterRating Rating of the waiter.
     * @param int $chefRating Rating of the chef.
     * @param ?string $comment Comment of the Survey, defaults to null.
     * @param ?int $id ID of the Survey, defaults to null.
     */
    public function __construct(
        public string $orderId,
        public int $tableRating,
        public int $restaurantRating,
        public int $waiterRating,
        public int $chefRating,
        public ?string $comment = null,
        ?int $id = null
    )
    {
        parent::__construct($id);
    }

    /**
     * @return float The average score of the survey.
     */
    public function getAverageScore(): float
    {
        return ($this->tableRating + $this->restaurantRating + $this->waiterRating + $this->chefRating) / 4;
    }

    /**
     * @inheritdoc Entity::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        return [
            'idPedido' => $this->orderId,
            'puntuacionMesa' => $this->tableRating,
            'puntuacionRestaurante' => $this->restaurantRating,
            'puntuacionCamarero' => $this->waiterRating,
            'puntuacionCocinero' => $this->chefRating,
            'comentario' => $this->comment,
            'id' => $this->id,
        ];
    }
}