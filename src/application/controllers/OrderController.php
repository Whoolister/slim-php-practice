<?php

declare(strict_types=1);

namespace App\application\controllers;

use App\application\services\OrderService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function json_encode;

class OrderController
{
    private const ID_KEY = 'id';

    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function getAll(Request $request, Response $response): Response
    {
        $orders = $this->orderService->getAll();

        $response->getBody()->write(json_encode(['pedidos' => $orders]));

        return $response->withStatus(200, 'OK');
    }

    public function getById(Request $request, Response $response, $id): Response
    {
        $order = $this->orderService->getById($id);

        if ($order === false) {
            return $response->withStatus(404, 'No se encontró el pedido');
        }

        $response->getBody()->write(json_encode(['pedido' => $order]));

        return $response->withStatus(200, 'OK');
    }

    public function create(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        unset($body[self::ID_KEY]);

        $order = $this->orderService->createOrder($body);

        $result = $this->orderService->save($order);

        $response->getBody()->write(json_encode(['pedido' => $result]));

        return $response->withStatus(201, 'Creado');
    }

    public function delete(Request $request, Response $response, $id): Response
    {
        if ($this->orderService->delete($id) === false) {
            return $response->withStatus(500, 'No se pudo eliminar la orden');
        }

        return $response->withStatus(200, 'OK');
    }
}