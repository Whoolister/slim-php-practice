<?php

declare(strict_types=1);

namespace App\entities\orders;

use App\entities\Entity;

final class Survey extends Entity
{
    private int $orderId;
    private int $tableRating;
    private int $restaurantRating;
    private int $waiterRating;
    private int $chefRating;
    private ?string $comment;


    /**
     * @return int ID of the Order that this survey belongs to.
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId ID of the Order that this survey belongs to.
     */
    public function setOrderId(int $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @return int Rating of the table.
     */
    public function getTableRating(): int
    {
        return $this->tableRating;
    }

    /**
     * @param int $tableRating Rating of the table.
     */
    public function setTableRating(int $tableRating): void
    {
        $this->tableRating = $tableRating;
    }

    /**
     * @return int Rating of the restaurant.
     */
    public function getRestaurantRating(): int
    {
        return $this->restaurantRating;
    }

    /**
     * @param int $restaurantRating Rating of the restaurant.
     */
    public function setRestaurantRating(int $restaurantRating): void
    {
        $this->restaurantRating = $restaurantRating;
    }

    /**
     * @return int Rating of the waiter.
     */
    public function getWaiterRating(): int
    {
        return $this->waiterRating;
    }

    /**
     * @param int $waiterRating Rating of the waiter.
     */
    public function setWaiterRating(int $waiterRating): void
    {
        $this->waiterRating = $waiterRating;
    }

    /**
     * @return int Rating of the chef.
     */
    public function getChefRating(): int
    {
        return $this->chefRating;
    }

    /**
     * @param int $chefRating Rating of the chef.
     */
    public function setChefRating(int $chefRating): void
    {
        $this->chefRating = $chefRating;
    }

    /**
     * @return ?string Comment of the Survey.
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param ?string $comment Comment of the Survey.
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return ?int ID of the Survey.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param ?int $id ID of the Survey.
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Constructs a Survey
     *
     * @param int $orderId ID of the Order that this survey belongs to.
     * @param int $tableRating Rating of the table.
     * @param int $restaurantRating Rating of the restaurant.
     * @param int $waiterRating Rating of the waiter.
     * @param int $chefRating Rating of the chef.
     * @param ?string $comment Comment of the Survey, defaults to null.
     * @param ?int $id ID of the Survey, defaults to null.
     */
    public function __construct(
        int $orderId,
        int $tableRating,
        int $restaurantRating,
        int $waiterRating,
        int $chefRating,
        ?string $comment = null,
        ?int $id = null
    )
    {
        $this->setOrderId($orderId);
        $this->setTableRating($tableRating);
        $this->setRestaurantRating($restaurantRating);
        $this->setWaiterRating($waiterRating);
        $this->setChefRating($chefRating);
        $this->setComment($comment);
        $this->setId($id);
    }


    /**
     * @return array{ idPedido: int, puntuacionMesa: int, puntuacionRestaurante:int, puntuacionRestaurante: int, puntuacionCamarero: int , puntuacionCocinero: int, comentario: ?string, id: int } Array representation of the Survey.
     */
    public function jsonSerialize(): array
    {
        return [
            'idPedido' => $this->getOrderId(),
            'puntuacionMesa' => $this->getTableRating(),
            'puntuacionRestaurante' => $this->getRestaurantRating(),
            'puntuacionCamarero' => $this->getWaiterRating(),
            'puntuacionCocinero' => $this->getChefRating(),
            'comentario' => $this->getComment(),
            'id' => $this->getId(),
        ];
    }
}