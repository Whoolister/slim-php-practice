<?php

declare(strict_types=1);

namespace App\services;

use App\entities\orders\Order;
use App\repositories\OrderRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

readonly class OrderService
{
    public function __construct(private OrderRepository $orderRepository)
    {
    }

    /**
     * @return Order[] Returns all the orders in the database
     */
    public function GetAll(Request $request, Response $response, array $args): array
    {
        // TODO: Implement GetAll() method.
    }

    public function GetOne(Request $request, Response $response, array $args): Response
    {
        // TODO: Implement GetAll() method.
    }

    public function Add(Request $request, Response $response, array $args): Response
    {
        // TODO: Implement GetAll() method.
    }

    public function Update(Request $request, Response $response, array $args): Response
    {
        // TODO: Implement GetAll() method.
    }

    public function Delete(Request $request, Response $response, array $args): Response
    {
        // TODO: Implement GetAll() method.
    }
}