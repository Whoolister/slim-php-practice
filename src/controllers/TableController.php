<?php

declare(strict_types=1);

namespace App\controllers;

use App\entities\tables\Table;
use App\entities\tables\TableStatus;
use App\services\TableService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final readonly class TableController
{
    private const ID_KEY = 'idMesa';
    private const STATUS_KEY = 'estado';

    public function __construct(private TableService $tableService)
    {
    }

    public function getAll(Request $request, Response $response, array $args): Response
    {
        $response->getBody()->write(json_encode($this->tableService->getAll()));

        return $response->withStatus(200, 'OK');
    }

    public function GetOne(Request $request, Response $response, array $args): Response
    {
        if (($id = $args[self::ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (($table = $this->tableService->getOne($id)) === false) {
            return $response->withStatus(404, 'No se encontró la mesa');
        }

        $response->getBody()->write(json_encode($table));

        return $response->withStatus(200, 'OK');
    }

    public function add(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();

        if (($status = $body[self::STATUS_KEY]) === null) {
            return $response->withStatus(400, 'Falta el estado');
        }

        if (($status = TableStatus::tryFrom($status)) === null) {
            return $response->withStatus(400, 'El estado no es válido');
        }

        if (($table = $this->tableService->add(new Table($status))) === false) {
            return $response->withStatus(500, 'No se pudo agregar la mesa');
        }

        $response->getBody()->write(json_encode($table));

        return $response->withStatus(201, 'Creada');
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        if (($id = $args[self::ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        $body = $request->getParsedBody();

        if (($status = $body[self::STATUS_KEY]) === null) {
            return $response->withStatus(400, 'Falta el estado');
        }

        if (($status = TableStatus::tryFrom($status)) === null) {
            return $response->withStatus(400, 'El estado no es válido');
        }

        if (($table = $this->tableService->update(new Table($status, id: $id))) === false) {
            return $response->withStatus(500, 'No se pudo actualizar la mesa');
        }

        $response->getBody()->write(json_encode($table));

        return $response->withStatus(200, 'OK');
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        if (($id = $args[self::ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if ($this->tableService->delete($id) === false) {
            return $response->withStatus(500, 'No se pudo eliminar la mesa');
        }

        return $response->withStatus(204, 'OK');
    }
}