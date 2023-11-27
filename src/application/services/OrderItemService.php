<?php

declare(strict_types=1);

namespace App\application\services;

use App\domain\exceptions\DomainException;
use App\domain\orders\Order;
use App\domain\orders\OrderItem;
use App\domain\orders\OrderItemRepository;
use App\domain\orders\OrderItemStatus;
use App\domain\products\ProductType;
use function is_string;
use function preg_match;

class OrderItemService
{
    private const ID_KEY = 'id';
    private const ORDER_ID_KEY = 'idPedido';
    private const PRODUCT_ID_KEY = 'idProducto';

    /**
     * Constructs an Order Item Service.
     *
     * @param OrderItemRepository $orderItemRepository The order item repository to use.
     */
    public function __construct(private OrderItemRepository $orderItemRepository)
    {
    }

    /**
     * @return OrderItem[] All the orders currently persisted
     */
    public function getAll(): array
    {
        return $this->orderItemRepository->getAll();
    }

    /**
     * Gets all the pending order items for a given type.
     *
     * @param ProductType $productType The type of product to get the order items for.
     * @return OrderItem[] All the order items of the given type.
     */
    public function getAllPendingByType(ProductType $productType): array
    {
        return $this->orderItemRepository->getAllPendingByType($productType);
    }

    /**
     * @return OrderItem[] All the pending order items
     */
    public function getAllPending(): array
    {
        return $this->orderItemRepository->getAllPending();
    }

    /**
     * Gets all the order items for a given order.
     *
     * @param string $orderId The ID of the order to get the order items for.
     * @return OrderItem[] All the order items for the given order.
     */
    public function getAllByOrderId(string $orderId): array
    {
        return $this->orderItemRepository->getAllByOrderId($orderId);
    }

    /**
     * Gets an order item by its ID.
     *
     * @param int $id The ID of the order item to get.
     * @return false|OrderItem The order item with the given ID, or false if it doesn't exist.
     */
    public function getById(int $id): false|OrderItem
    {
        return $this->orderItemRepository->getById($id);
    }

    /**
     * Gets the price of an order.
     *
     * @param string $orderId The ID of the order to get the price for.
     * @return float The price of the order.
     */
    public function getPrice(string $orderId): float
    {
        return $this->orderItemRepository->getPrice($orderId);
    }

    /**
     * Gets the pending time for an order.
     *
     * @param string $orderId The ID of the order to get the pending time for.
     * @return int The pending time for the order.
     */
    public function getPendingTime(string $orderId): int {
        return $this->orderItemRepository->getPendingTime($orderId);
    }

    /**
     * Saves an order item.
     *
     * @param OrderItem $orderItem The order item to save.
     * @return OrderItem The saved order item.
     * @throws DomainException If the order item could not be saved.
     */
    public function save(OrderItem $orderItem): OrderItem
    {
        if (($result = $this->orderItemRepository->save($orderItem)) === false) {
            throw new DomainException('No se pudo guardar el item del pedido.', 500);
        }
        return $result;
    }

    /**
     * Saves multiple order items.
     *
     * @param OrderItem[] $orderItems The order items to save.
     * @return bool Whether the order items were saved or not.
     */
    public function saveMultiple(array $orderItems): bool
    {
        return $this->orderItemRepository->saveMultiple($orderItems);
    }

    /**
     * Deletes an order item.
     *
     * @param int $id The ID of the order item to delete.
     * @return bool Whether the order item was deleted or not.
     * @throws DomainException If the order item does not exist.
     */
    public function delete(int $id): bool
    {
        if ($this->orderItemRepository->existsById($id) === false) {
            throw new DomainException('No se encontró el item del pedido.', 404);
        }

        return $this->orderItemRepository->deleteById($id);
    }

    /**
     * Deletes all the order items for an order.
     *
     * @param string $orderId The ID of the order to delete the items for.
     * @return bool Whether the order items were deleted or not.
     */
    public function deleteByOrderId(string $orderId): bool
    {
        return $this->orderItemRepository->deleteByOrderId($orderId);
    }

    public function createOrderItem(mixed $orderItemData): OrderItem
    {
        if ($orderItemData === null) {
            throw new DomainException('Los datos del item no pueden estar vacios.', 400);
        }

        if (!isset($orderItemData[self::ORDER_ID_KEY])) {
            throw new DomainException('Falta el ID del pedido.', 400);
        }

        if (!isset($orderItemData[self::PRODUCT_ID_KEY])) {
            throw new DomainException('Falta el ID del producto.', 400);
        }

        $id = $orderItemData[self::ID_KEY] ?? null;
        $orderId = $orderItemData[self::ORDER_ID_KEY];
        $productId = $orderItemData[self::PRODUCT_ID_KEY];

        if ($id !== null && !is_numeric($id)) {
            throw new DomainException('El ID debe ser un número.', 400);
        }

        if ($id !== null && (!is_string($orderId) || preg_match('/^\w{5}$/', $id) === false)) {
            throw new DomainException('El ID del pedido debe ser un codigo alfanumerico de 5 digitos.', 400);
        }

        if (!is_numeric($productId)) {
            throw new DomainException('El ID del producto debe ser un número.', 400);
        }

        return new OrderItem(
            orderId: $orderId,
            productId: (int) $productId,
            id: (int) $id
        );
    }
}