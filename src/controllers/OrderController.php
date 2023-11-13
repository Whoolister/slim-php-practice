<?php

declare(strict_types=1);

namespace App\controllers;

use App\entities\orders\Order;
use App\services\OrderService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function is_numeric;
use function json_encode;

final readonly class OrderController
{
    private const ID_KEY = 'idPedido';
    private const TABLE_ID_KEY = 'idMesa';
    private const CLIENT_NAME_KEY = 'nombreCliente';

    public function __construct(private OrderService $orderService)
    {
    }

    public function getAll(Request $request, Response $response, array $args): Response
    {
        if (($tableId = $args[self::TABLE_ID_KEY]) === null) {
            $response->getBody()->write(json_encode($this->orderService->getAll()));

            return $response->withStatus(200, 'OK');
        }

        $response->getBody()->write(json_encode($this->orderService->getAllByTableId($tableId)));

        return $response->withStatus(200, 'OK');
    }

    public function getOne(Request $request, Response $response, array $args): Response
    {
        if (($id = $args[self::ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (($tableId = $args[self::TABLE_ID_KEY]) === null) {
            if (($order = $this->orderService->getOne((int) $id)) === false) {
                return $response->withStatus(404, 'No se encontró la orden');
            }

            $response->getBody()->write(json_encode($order));

            return $response->withStatus(200, 'OK');
        }

        if (($order = $this->orderService->getOneByTableId((int) $id, $tableId)) === false) {
            return $response->withStatus(404, 'No se encontró la orden');
        }

        $response->getBody()->write(json_encode($order));

        return $response->withStatus(200, 'OK');
    }

    public function add(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();

        if (($tableId = $args[self::TABLE_ID_KEY] ?? $body[self::TABLE_ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID de la mesa');
        }

        if (($clientName = $body[self::CLIENT_NAME_KEY]) === null) {
            return $response->withStatus(400, 'Falta el nombre del cliente');
        }

        $response->getBody()->write(json_encode($this->orderService->add(new Order($tableId, $clientName))));

        return $response->withStatus(201, 'Creado');
    }

    public function update(Request $request, Response $response, array $args): Response
    {

        if (($id = $args[self::ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (!is_numeric($id)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        $body = $request->getParsedBody();

        if (($tableId = $args[self::TABLE_ID_KEY] ?? $body[self::TABLE_ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID de la mesa');
        }

        if (($clientName = $body[self::CLIENT_NAME_KEY]) === null) {
            return $response->withStatus(400, 'Falta el nombre del cliente');
        }

        if (($order = $this->orderService->update(new Order($tableId, $clientName, $id))) === false) {
            return $response->withStatus(500, 'No se pudo actualizar la orden');
        }

        $response->getBody()->write(json_encode($order));

        return $response->withStatus(200, 'OK');
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        if (($id = $args[self::ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (!is_numeric($id)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        if (($tableId = $args[self::TABLE_ID_KEY]) === null) {
            if ($this->orderService->delete((int) $id) === false) {
                return $response->withStatus(500, 'No se pudo eliminar la orden');
            }

            return $response->withStatus(200, 'OK');
        }

        if ($this->orderService->deleteByTableId((int) $id, $tableId) === false) {
            return $response->withStatus(500, 'No se pudo eliminar la orden');
        }

        return $response->withStatus(200, 'OK');
    }
}