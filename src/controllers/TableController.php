<?php

declare(strict_types=1);

namespace App\controllers;

use App\entities\tables\TableStatus;
use App\services\TableService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function filter_var;
use const FILTER_NULL_ON_FAILURE;
use const FILTER_VALIDATE_BOOLEAN;

readonly class TableController
{
    public function __construct(private TableService $tableService)
    {
    }

    public function GetAll(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode($this->tableService->GetAll()));

        return $response->withStatus(200, 'OK');
    }

    public function GetOne(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        if (($id = $args['id']) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (($table = $this->tableService->GetById($id)) === false) {
            return $response->withStatus(404, 'No se encontró la mesa');
        }

        $response->getBody()->write(json_encode($table));

        return $response->withStatus(200, 'OK');
    }

    public function Add(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        $body = $request->getParsedBody();

        if (($status = $body['estado']) === null) {
            return $response->withStatus(400, 'Falta el estado');
        }

        if (($table = $this->tableService->Add($status)) === false) {
            return $response->withStatus(500, 'No se pudo agregar la mesa');
        }

        $response->getBody()->write(json_encode($table));

        return $response->withStatus(201, 'Creada');
    }

    public function Update(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        if (($id = $args['id']) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        $body = $request->getParsedBody();

        if (($status = $body['estado']) === null) {
            return $response->withStatus(400, 'Falta el estado');
        }

        if (($status = TableStatus::tryFrom($status)) === null) {
            return $response->withStatus(400, 'El estado no es válido');
        }

        if (($active = $body['activo']) === null) {
            return $response->withStatus(400, 'Falta el estado de actividad');
        }

        if (($active = filter_var($active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) === null) {
            return $response->withStatus(400, 'El estado de actividad no es válido');
        }

        if (($table = $this->tableService->Update($id, $status, $active)) === false) {
            return $response->withStatus(500, 'No se pudo actualizar la mesa');
        }

        $response->getBody()->write(json_encode($table));

        return $response->withStatus(200, 'OK');
    }

    public function Delete(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        if (($id = $args['id']) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if ($this->tableService->Delete($id) === false) {
            return $response->withStatus(500, 'No se pudo eliminar la mesa');
        }

        return $response->withStatus(204, 'OK');
    }
}