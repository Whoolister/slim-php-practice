<?php

declare(strict_types=1);

namespace App\application\services;

use App\domain\exceptions\DomainException;
use App\domain\orders\Order;
use App\domain\orders\OrderRepository;
use App\domain\orders\OrderStatus;
use function is_numeric;
use function is_string;
use function preg_match;

class OrderService
{
    private const ID_KEY = 'id';
    private const TABLE_ID_KEY = 'idMesa';
    private const CLIENT_NAME_KEY = 'nombreCliente';

    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return Order[] Returns all the orders in the database
     */
    public function getAll(): array
    {
        return $this->orderRepository->getAll();
    }

    /**
     * Gets all orders with a given status.
     *
     * @param OrderStatus $status The status to get the orders for.
     * @return Order[] All the orders with the given status.
     */
    public function getAllByStatus(OrderStatus $status): array
    {
        return $this->orderRepository->getAllByStatus($status);
    }

    /**
     * Gets an order by its ID.
     *
     * @param string $id The ID of the order to get.
     * @return false|Order The order with the given ID, or false if it doesn't exist.
     */
    public function getById(string $id): false|Order
    {
        return $this->orderRepository->getById($id);
    }

    /**
     * Gets the active order for a table.
     *
     * @param int $tableId The ID of the table to get the active order for.
     * @return false|Order The active order for the given table, or false if it doesn't exist.
     */
    public function getActiveOrderForTable(int $tableId): false|Order
    {
        return $this->orderRepository->getActiveOrderByTableId($tableId);
    }

    /**
     * Saves an order.
     *
     * @param Order $user The order to save.
     * @return Order The saved order.
     * @throws DomainException If the order could not be saved.
     */
    public function save(Order $user): Order
    {
        if (($result = $this->orderRepository->save($user)) === false) {
            throw new DomainException('No se pudo guardar el pedido', 500);
        }

        return $result;
    }

    /**
     * Deletes an order.
     *
     * @param string $id The ID of the order to delete.
     * @return bool Whether the order was deleted or not.
     * @throws DomainException If the order does not exist.
     */
    public function delete(string $id): bool
    {
        if ($this->orderRepository->existsById($id) === false) {
            throw new DomainException('No se encontró el pedido.', 404);
        }

        return $this->orderRepository->deleteById($id);
    }

    /**
     * Creates an order from the given data.
     *
     * @param mixed $orderData The data to create the order from.
     * @return Order The created order.
     * @throws DomainException If the data is invalid.
     */
    public function createOrder(mixed $orderData): Order {
        if ($orderData === null) {
            throw new DomainException('Los datos del pedido no pueden estar vacios.', 400);
        }

        if (!isset($orderData[self::TABLE_ID_KEY])) {
            throw new DomainException('Falta el ID de la mesa.', 400);
        }

        if (!isset($orderData[self::CLIENT_NAME_KEY])) {
            throw new DomainException('Falta el nombre del cliente', 400);
        }

        $id = $orderData[self::ID_KEY] ?? null;
        $tableId = $orderData[self::TABLE_ID_KEY];
        $clientName = $orderData[self::CLIENT_NAME_KEY];

        if ($id !== null && preg_match('/^\w{5}$/', $id)) {
            throw new DomainException('El ID del pedido debe ser un codigo alfanumerico de 5 digitos.', 400);
        }

        if (!is_numeric($tableId)) {
            throw new DomainException('El ID de la mesa debe ser un número.', 400);
        }

        if (empty($clientName)) {
            throw new DomainException('El nombre del cliente no puede estar vacio.', 400);
        }

        return new Order(
            tableId: (int) $tableId,
            clientName: $clientName,
            status: OrderStatus::PENDING,
            id: $id === null ? null : $id
        );
    }
}