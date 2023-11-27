<?php
declare(strict_types=1);

namespace App\application\controllers;

use App\application\services\OrderItemService;
use App\domain\orders\OrderItem;
use App\domain\orders\OrderItemStatus;
use App\domain\products\ProductType;
use App\domain\users\UserRole;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function is_numeric;
use function json_encode;

class OrderItemController
{
    public function __construct(private OrderItemService $orderItemService)
    {
    }

    public function getAll(Request $request, Response $response): Response
    {
        $response->getBody()->write(json_encode(['items' => $this->orderItemService->getAll()]));

        return $response->withStatus(200, 'OK');
    }

    public function getAllPending(Request $request, Response $response): Response
    {
        $role = $request->getAttribute(UserRole::class);

        $items = match ($role) {
            UserRole::BREWER => $this->orderItemService->getAllPendingByType(ProductType::BEER),
            UserRole::BARTENDER => $this->orderItemService->getAllPendingByType(ProductType::WINE_OR_DRINK),
            UserRole::CHEF => $this->orderItemService->getAllPendingByType(ProductType::MEAL),
            UserRole::BAKER => $this->orderItemService->getAllPendingByType(ProductType::PASTRIES),
            default => $this->orderItemService->getAllPending(),
        };

        $response->getBody()->write(json_encode(['items' => $items]));

        return $response->withStatus(200, 'OK');
    }

    public function getById(Request $request, Response $response, $id): Response
    {
        $orderItem = $this->orderItemService->getById((int) $id);

        $response->getBody()->write(json_encode(['item' => $orderItem]));

        return $response->withStatus(200, 'OK');
    }

    public function delete(Request $request, Response $response, $id): Response
    {
        if (!$this->orderItemService->delete((int) $id)) {
            return $response->withStatus(500, 'No se pudo eliminar el item');
        }

        return $response->withStatus(200, 'OK');
    }
}