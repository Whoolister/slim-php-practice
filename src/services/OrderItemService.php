<?php

declare(strict_types=1);

namespace App\services;

use App\entities\orders\OrderItem;
use App\repositories\orders\OrderItemRepository;

final readonly class OrderItemService
{
    /**
     * Constructs an Order Item Service.
     *
     * @param OrderItemRepository $orderRepository The order item repository to use.
     */
    public function __construct(private OrderItemRepository $orderRepository)
    {
    }

    /**
     * @return OrderItem[] All the orders currently persisted
     */
    public function getAll(): array
    {
        return $this->orderRepository->getAll();
    }

    /**
     * Gets an order item by its ID.
     *
     * @param int $id The ID of the order item to get.
     * @return false|OrderItem The order item with the given ID, or false if it doesn't exist.
     */
    public function getOne(int $id): false|OrderItem
    {
        return $this->orderRepository->getById($id);
    }

    /**
     * Persists an order item.
     *
     * @param OrderItem $user The order item to persist.
     * @return false|OrderItem The created order item or false if it couldn't be created.
     */
    public function add(OrderItem $user): false|OrderItem
    {
        if ($user->getId() !== null) {
            return false;
        }

        return $this->orderRepository->save($user);
    }

    /**
     * Updates an order item.
     *
     * @param OrderItem $user The order item to update.
     * @return false|OrderItem The updated order item or false if it couldn't be updated.
     */
    public function update(OrderItem $user): false|OrderItem
    {
        if (!$this->orderRepository->existsById($user->getId())) {
            return false;
        }

        return $this->orderRepository->save($user);
    }

    /**
     * Deletes an order item.
     *
     * @param int $id The ID of the order item to delete.
     * @return bool Whether the order item was deleted or not.
     */
    public function delete(int $id): bool
    {
        return $this->orderRepository->deleteById($id);
    }
}