<?php

declare(strict_types=1);

namespace App\application\controllers;

use App\application\services\TableService;
use App\domain\tables\Table;
use App\domain\tables\TableStatus;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function json_encode;

class TableController
{
    private const STATUS_KEY = 'estado';

    private TableService $tableService;

    public function __construct(TableService $tableService)
    {
        $this->tableService = $tableService;
    }

    public function getAll(Request $request, Response $response): Response
    {
        $queryParams = $request->getQueryParams();

        if (isset($queryParams[self::STATUS_KEY])) {
            $status = TableStatus::tryFrom($queryParams[self::STATUS_KEY]);

            if ($status === null) {
                return $response->withStatus(400, 'El estado no es válido');
            }

            $response->getBody()->write(json_encode(['mesas' => $this->tableService->getAllByStatus($status)]));

            return $response->withStatus(200, 'OK');
        }

        $response->getBody()->write(json_encode(['mesas' => $this->tableService->getAll()]));

        return $response->withStatus(200, 'OK');
    }


    public function getMostPopular(Request $request, Response $response): Response
    {
        if (($table = $this->tableService->getMostPopular()) === false) {
            return $response->withStatus(404, 'No se encontró la mesa');
        }

        $response->getBody()->write(json_encode(['mesa' => $table]));

        return $response->withStatus(200, 'OK');
    }

    public function getById(Request $request, Response $response, $id): Response
    {
        if (($table = $this->tableService->getById((int) $id)) === false) {
            return $response->withStatus(404, 'No se encontró la mesa');
        }

        $response->getBody()->write(json_encode(['mesa' => $table]));

        return $response->withStatus(200, 'OK');
    }

    public function create(Request $request, Response $response): Response
    {
        $table = new Table(TableStatus::CLOSED);

        $result = $this->tableService->save($table);

        $response->getBody()->write(json_encode(['mesa' => $result]));

        return $response->withStatus(201, 'Creada');
    }

    public function delete(Request $request, Response $response, $id): Response
    {
        if (!$this->tableService->delete((int) $id)) {
            return $response->withStatus(500, 'No se pudo eliminar la mesa');
        }

        return $response->withStatus(204, 'OK');
    }
}