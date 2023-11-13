<?php
declare(strict_types=1);

namespace App\controllers;

use App\entities\orders\OrderItem;
use App\services\OrderItemService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function is_numeric;

final readonly class OrderItemController
{
    private const ID_KEY = 'idItemPedido';
    private const ORDER_ID_KEY = 'idPedido';
    private const PRODUCT_ID_KEY = 'idProducto';
    private const START_TIME_KEY = 'horaInicio';
    private const END_TIME_KEY = 'horaFin';

    public function __construct(private OrderItemService $orderItemService)
    {
    }

    public function getAll(Request $request, Response $response, array $args): Response
    {
        if (($orderId = $args[self::ORDER_ID_KEY]) === null) {
            $response->getBody()->write(json_encode($this->orderItemService->getAll()));

            return $response->withStatus(200, 'OK');
        }

        $response->getBody()->write(json_encode($this->orderItemService->getAllByOrderId($orderId)));

        return $response->withStatus(200, 'OK');
    }
    public function getOne(Request $request, Response $response, array $args): Response
    {
        if (($id = $args[self::ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (($orderId = $args[self::ORDER_ID_KEY]) === null) {
            if (($orderItem = $this->orderItemService->getOne((int) $id)) === false) {
                return $response->withStatus(404, 'No se encontró el item de la orden');
            }

            $response->getBody()->write(json_encode($orderItem));

            return $response->withStatus(200, 'OK');
        }

        if (($orderItem = $this->orderItemService->getOneByOrderId((int) $id, $orderId)) === false) {
            return $response->withStatus(404, 'No se encontró el item de la orden');
        }

        $response->getBody()->write(json_encode($orderItem));

        return $response->withStatus(200, 'OK');
    }

    public function add(Request $request, Response $response, array $args): Response
    {
        if (($orderId = $args[self::ORDER_ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID de la orden');
        }

        if (!is_numeric($orderId)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        $body = $request->getParsedBody();

        if (($productId = $args[self::PRODUCT_ID_KEY] ?? $body[self::PRODUCT_ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID del producto');
        }

        if (($orderItem = $this->orderItemService->add(new OrderItem($orderId, $productId))) === false) {
            return $response->withStatus(500, 'No se pudo agregar el item de la orden');
        }

        $response->getBody()->write(json_encode($orderItem));

        return $response->withStatus(201, 'Created');
    }
    public function update(Request $request, Response $response, array $args): Response
    {
        if (($orderItemId = $args[self::ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (!is_numeric($orderItemId)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        $body = $request->getParsedBody();

        $startTime = $body[self::START_TIME_KEY];
        $endTime = $body[self::END_TIME_KEY];

        if (($orderId = $body[self::ORDER_ID_KEY]) === null) {
            if (!is_numeric($orderId)) {
                return $response->withStatus(400, 'El ID debe ser un número');
            }

            if (($orderItem = $this->orderItemService->update(new OrderItem($orderId, $orderItemId, $startTime, $endTime))) === false) {
                return $response->withStatus(500, 'No se pudo actualizar el item de la orden');
            }

            $response->getBody()->write(json_encode($orderItem));

            return $response->withStatus(200, 'OK');
        }

    }
    public function delete(Request $request, Response $response, array $args): Response
    {
    }
}