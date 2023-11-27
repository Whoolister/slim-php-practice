<?php

declare(strict_types=1);

namespace App\application\controllers;

use App\application\services\OrderingService;
use App\domain\users\UserRole;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function abs;
use function array_map;
use function json_encode;

class OrderingController
{
    private const IMAGE_KEY = 'imagen';

    private OrderingService $orderingService;

    public function __construct(OrderingService $orderingService)
    {
        $this->orderingService = $orderingService;
    }

    public function order(Request $request, Response $response, $id): Response
    {
        $result = $this->orderingService->order((int) $id, $request->getParsedBody());

        $response->getBody()->write(json_encode(['pedido' => $result]));

        return $response->withStatus(201, 'Creado');
    }

    public function takePicture(Request $request, Response $response, $id): Response {
        $files = $request->getUploadedFiles();

        if (!isset($files[self::IMAGE_KEY])) {
            return $response->withStatus(400, 'Falta la imagen.');
        }

        if ($this->orderingService->takePicture((int) $id, $files[self::IMAGE_KEY]) === false) {
            return $response->withStatus(500, 'Error al guardar la imagen.');
        }

        return $response->withStatus(200, 'OK');
    }

    public function serve(Request $request, Response $response, $id): Response
    {
        $result = $this->orderingService->serve((int) $id);

        $response->getBody()->write(json_encode(['pedido' => $result]));

        return $response->withStatus(201, 'Creado');
    }

    public function charge(Request $request, Response $response, $id): Response
    {
        $price = $this->orderingService->getPrice((int) $id);

        $result = $this->orderingService->charge((int) $id);

        $response->getBody()->write(json_encode(['pedido' => $result, 'importe' => $price]));

        return $response->withStatus(201, 'Creado');
    }

    public function close(Request $request, Response $response, $id): Response
    {
        $result = $this->orderingService->close((int) $id);

        $response->getBody()->write(json_encode(['pedido' => $result]));

        return $response->withStatus(201, 'Creado');
    }

    public function getPendingTime(Request $request, Response $response, $tableId, $orderId): Response
    {
        $time = $this->orderingService->getPendingTime((int) $tableId, $orderId);

        if ($time >= 0) {
            $response->getBody()->write(json_encode(['demora' => "$time segundos"]));
        } else {
            $response->getBody()->write(json_encode(['demora' => 'Pedido demorado por' . abs($time) . ' segundos']));
        }

        return $response->withStatus(200, 'OK');
    }

    public function getAllPending(Request $request, Response $response): Response
    {
        $orders = $this->orderingService->getPendingOrders();

        // TODO: Simplify This
        $orders = array_map(
            function ($order) {
                $result = $order->jsonSerialize();
                $result['demora'] = $this->orderingService->getPendingTime($order->tableId, $order->id);

                return $result;
            },
            $orders
        );

        $response->getBody()->write(json_encode(['pedidos' => $orders]));

        return $response->withStatus(200, 'OK');
    }

    public function getAllReady(Request $request, Response $response): Response
    {
        $orders = $this->orderingService->getReadyOrders();

        $response->getBody()->write(json_encode(['pedidos' => $orders]));

        return $response->withStatus(200, 'OK');
    }

    public function startPreparation(Request $request, Response $response, $id): Response {
        $result = $this->orderingService->startPreparation((int) $id);

        $response->getBody()->write(json_encode(['item' => $result]));

        return $response->withStatus(200, 'OK');
    }
    public function finishPreparation(Request $request, Response $response, $id): Response {
        $result = $this->orderingService->finishPreparation((int) $id);

        $response->getBody()->write(json_encode(['item' => $result]));

        return $response->withStatus(200, 'OK');
    }
}