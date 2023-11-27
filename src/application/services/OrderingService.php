<?php

declare(strict_types=1);

namespace App\application\services;

use App\domain\exceptions\DomainException;
use App\domain\orders\Order;
use App\domain\orders\OrderItem;
use App\domain\orders\OrderItemStatus;
use App\domain\orders\OrderStatus;
use App\domain\tables\TableStatus;
use DateTime;
use Psr\Http\Message\UploadedFileInterface;
use function array_filter;
use function array_map;
use function count;
use function DI\value;
use function is_array;
use function var_dump;

class OrderingService
{
    private const ID_KEY = 'id';
    private const ORDER_ID_KEY = 'idPedido';
    private const TABLE_ID_KEY = 'idMesa';
    private const ITEMS_KEY = 'items';
    private const IMAGE_PATH = __DIR__ . '/../../../resources/uploads/OrderImages/';

    private TableService $tableService;
    private OrderService $orderService;
    private OrderItemService $orderItemService;
    private ImageService $imageService;

    public function __construct(TableService $tableService, OrderService $orderService, OrderItemService $orderItemService, ImageService $imageService)
    {
        $this->orderService = $orderService;
        $this->tableService = $tableService;
        $this->orderItemService = $orderItemService;
        $this->imageService = $imageService;
    }

    /**
     * Creates an order for a table.
     *
     * @param int $tableId The ID of the table to order to.
     * @param array $orderData The data of the order to create.
     * @return Order The created order.
     * @throws DomainException If the table does not exist, or if it is not taking orders, or if the order is invalid.
     */
    public function order(int $tableId, mixed $orderData): Order
    {
        if ($orderData === null) {
            throw new DomainException('Faltan los datos del pedido.', 400);
        }

        if (($table = $this->tableService->getById($tableId)) === false) {
            throw new DomainException('La mesa no existe.', 404);
        }

        if ($table->status !== TableStatus::CLOSED) {
            throw new DomainException('La mesa no está disponible.', 409);
        }

        $table->status = TableStatus::WAITING_FOR_ORDER;

        unset($orderData[self::ID_KEY]);
        $orderData[self::TABLE_ID_KEY] = $tableId;

        $order = $this->orderService->createOrder($orderData);

        if (!isset($orderData[self::ITEMS_KEY]) || !is_array($orderData[self::ITEMS_KEY]) || count($orderData[self::ITEMS_KEY]) === 0) {
            throw new DomainException('El pedido no tiene items.', 400);
        }

        $order = $this->orderService->save($order);

        $orderItems = array_map(
            function ($orderData) use ($order) {
                $orderData[self::ORDER_ID_KEY] = $order->id;
                return $this->orderItemService->createOrderItem($orderData);
            },
            $orderData[self::ITEMS_KEY]
        );

        if ($this->orderItemService->saveMultiple($orderItems) === false) {
            $this->orderService->delete($order->id);

            throw new DomainException('No se pudo guardar los items del pedido.', 500);
        }

        try {
            $this->tableService->save($table);
        } catch (DomainException $e) {
            $this->orderService->delete($order->id);
            $this->orderItemService->deleteByOrderId($order->id);

            throw new DomainException('No se pudo asignar el pedido a la mesa.', 500);
        }


        return $order;
    }

    /**
     * Takes a picture of a table's order.
     *
     * @param int $tableId The ID of the table to take a picture of.
     * @param UploadedFileInterface $image The image to save.
     * @return bool Whether the image was saved successfully.
     * @throws DomainException If the table does not exist, or if it doesn't have an active order.
     */
    public function takePicture(int $tableId, UploadedFileInterface $image): bool
    {
        if ($this->tableService->getById($tableId) === false) {
            throw new DomainException('La mesa no existe.', 404);
        }

        if (($order = $this->orderService->getActiveOrderForTable($tableId)) === false) {
            throw new DomainException('La mesa no tiene un pedido activo.', 409);
        }

        return $this->imageService->save($image, self::IMAGE_PATH, "$tableId-$order->id.jpg");
    }

    /**
     * Sets the status of a table to 'eating', allowing the customers to eat.
     *
     * @param int $tableId The ID of the table to serve an order to.
     * @return Order The served order.
     * @throws DomainException If the table does not exist, or if it is not waiting for an order, or if the order is not ready.
     */
    public function serve(int $tableId): Order
    {
        if (($table = $this->tableService->getById($tableId)) === false) {
            throw new DomainException('La mesa no existe.', 404);
        }

        if ($table->status !== TableStatus::WAITING_FOR_ORDER) {
            throw new DomainException('La mesa no está esperando un pedido.', 409);
        }

        if (($order = $this->orderService->getActiveOrderForTable($tableId)) === false) {
            throw new DomainException('La mesa no tiene un pedido activo.', 409);
        }

        if ($order->status !== OrderStatus::READY) {
            throw new DomainException('El pedido no está listo para servir.', 409);
        }

        $table->status = TableStatus::EATING;

        $order->status = OrderStatus::SERVED;

        $this->orderService->save($order);

        try {
            $this->tableService->save($table);
        } catch (DomainException $e) {
            $order->status = OrderStatus::READY;
            $this->orderService->save($order);

            throw new DomainException('No se pudo servir el pedido.', 500);
        }

        return $order;
    }

    /**
     * Sets the status of a table to 'paying', allowing the customers to pay, and the partners to close the table.
     *
     * @param int $tableId The ID of the table to charge.
     * @return Order Whether the table had an order charged successfully.
     * @throws DomainException If the table does not exist, or if it is not eating, or if the order is not served.
     */
    public function charge(int $tableId): Order
    {
        if (($table = $this->tableService->getById($tableId)) === false) {
            throw new DomainException('La mesa no existe.', 404);
        }

        if ($table->status !== TableStatus::EATING) {
            throw new DomainException('La mesa no tiene un pedido esperando cobro.', 409);
        }

        if (($order = $this->orderService->getActiveOrderForTable($tableId)) === false) {
            throw new DomainException('La mesa no tiene un pedido activo.', 409);
        }

        if ($order->status !== OrderStatus::SERVED) {
            throw new DomainException('El pedido no está servido.', 409);
        }

        $table->status = TableStatus::PAYING;

        try {
            $this->tableService->save($table);
        } catch (DomainException $e) {
            throw new DomainException('No se pudo servir el pedido.', 500);
        }

        return $order;
    }

    /**
     * Sets the status of a table to 'closed', allowing it to be used by other customers.
     *
     * @param int $id The ID of the table to close.
     * @return Order Whether the table had an order closed successfully.
     * @throws DomainException If the table does not exist, or if it is not paying, or if the order is not paid.
     */
    public function close(int $id): Order
    {
        if (($table = $this->tableService->getById($id)) === false) {
            throw new DomainException('La mesa no existe.', 404);
        }

        if ($table->status !== TableStatus::PAYING) {
            throw new DomainException('La mesa no tiene un pedido por pagar.', 409);
        }

        if (($order = $this->orderService->getActiveOrderForTable($id)) === false) {
            throw new DomainException('La mesa no tiene un pedido activo.', 409);
        }

        $table->status = TableStatus::CLOSED;

        $order->status = OrderStatus::PAID;

        try {
            $this->tableService->save($table);
        } catch (DomainException $e) {
            $order->status = OrderStatus::SERVED;
            $this->orderService->save($order);

            throw new DomainException('No se pudo cerrar la mesa.', 500);
        }

        return $order;
    }

    public function getPrice(int $tableId): float
    {
        if (($table = $this->tableService->getById($tableId)) === false) {
            throw new DomainException('La mesa no existe.', 404);
        }

        if (($order = $this->orderService->getActiveOrderForTable($tableId)) === false) {
            throw new DomainException('El pedido no existe.', 404);
        }

        return $this->orderItemService->getPrice($order->id);
    }

    public function getPendingTime(int $tableId, string $orderId): int
    {
        if (($table = $this->tableService->getById($tableId)) === false) {
            throw new DomainException('La mesa no existe.', 404);
        }

        if (($order = $this->orderService->getById($orderId)) === false) {
            throw new DomainException('El pedido no existe.', 404);
        }

        if ($table->status !== TableStatus::WAITING_FOR_ORDER) {
            throw new DomainException('La mesa no está esperando un pedido.', 409);
        }

        return $this->orderItemService->getPendingTime($orderId);
    }


    /**
     * @return Order[] All the pending orders.
     */
    public function getPendingOrders(): array {
        // TODO: Fix this
        return $this->orderService->getAllByStatus(OrderStatus::PENDING) + $this->orderService->getAllByStatus(OrderStatus::PREPARING);
    }

    /**
     * @return Order[] All the ready orders.
     */
    public function getReadyOrders(): array {
        return $this->orderService->getAllByStatus(OrderStatus::READY);
    }

    public function startPreparation(int $id): OrderItem
    {
        if (($orderItem = $this->orderItemService->getById($id)) === false) {
            throw new DomainException('El item no existe.', 404);
        }

        if ($orderItem->getStatus() !== OrderItemStatus::PENDING) {
            throw new DomainException('El item no está esperando a ser preparado', 409);
        }

        $orderItem->startTime = new DateTime();

        $order = $this->orderService->getById($orderItem->orderId);

        if ($order->status !== OrderStatus::PREPARING) {
            $order->status = OrderStatus::PREPARING;

            $this->orderService->save($order);
        }

        return $this->orderItemService->save($orderItem);
    }

    public function finishPreparation(int $id): OrderItem
    {
        if (($orderItem = $this->orderItemService->getById($id)) === false) {
            throw new DomainException('El item no existe.', 404);
        }

        if ($orderItem->getStatus() !== OrderItemStatus::PREPARING) {
            throw new DomainException('El item no está siendo preparado', 409);
        }

        $orderItem->endTime = new DateTime();

        $orderItem = $this->orderItemService->save($orderItem);

        $order = $this->orderService->getById($orderItem->orderId);

        $orderItems = $this->orderItemService->getAllByOrderId($orderItem->orderId);

        $allFinished = count(array_filter($orderItems, fn($item) => $item->getStatus() !== OrderItemStatus::READY)) === 0;

        if ($allFinished) {
            $order->status = OrderStatus::READY;

            $this->orderService->save($order);
        }

        return $orderItem;
    }
}