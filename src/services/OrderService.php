<?php

declare(strict_types=1);

namespace App\services;

use App\entities\orders\Order;
use App\entities\users\User;
use App\repositories\orders\OrderRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final readonly class OrderService
{
    public function __construct(private OrderRepository $orderRepository)
    {
    }

    /**
     * @return Order[] Returns all the orders in the database
     */
    public function getAll(): array
    {
        return $this->orderRepository->getAll();
    }

    public function getAllByTableId(string $tableId): array
    {
        return $this->orderRepository->getAllByTableId($tableId);
    }

    public function getOne(int $id): false|Order
    {
        return $this->orderRepository->getById($id);
    }

    public function getOneByTableId(int $id, string $tableId): array
    {
        return $this->orderRepository->getByTableId($tableId);
    }

    public function add(Order $user): false|Order
    {
        if ($user->getId() !== null) {
            return false;
        }

        return $this->orderRepository->save($user);
    }

    /**
     * @param Order $user
     * @return false|Order Returns the updated order or false if it couldn't be updated.
     */
    public function update(Order $user): false|Order
    {
        if (!$this->orderRepository->existsById($user->getId())) {
            return false;
        }

        return $this->orderRepository->save($user);
    }

    /**
     * Deletes an order from the database.
     *
     * @param int $id The ID of the order to delete.
     * @return bool Whether the order was deleted or not.
     */
    public function delete(int $id): bool
    {
        return $this->orderRepository->deleteById($id);
    }

    public function deleteByTableId(int $id, string $tableId): bool
    {
        return $this->orderRepository->deleteByTableId($id, $tableId);
    }
}