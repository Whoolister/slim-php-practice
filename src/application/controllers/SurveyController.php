<?php
declare(strict_types=1);

namespace App\application\controllers;

use App\application\services\SurveyService;
use App\domain\surveys\Survey;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function json_encode;
use function usort;

class SurveyController
{
    private const ID_KEY = 'id';
    private const TABLE_ID_KEY = 'idMesa';
    private const ORDER_ID_KEY = 'idPedido';

    private SurveyService $surveyService;

    public function __construct(SurveyService $surveyService)
    {
        $this->surveyService = $surveyService;
    }

    public function getAll(Request $request, Response $response): Response
    {
        $surveys = $this->surveyService->getAll();
        usort(
            $surveys,
            fn(Survey $a, Survey $b) => $a->getAverageScore() <=> $b->getAverageScore()
        );

        $response->getBody()->write(json_encode(['encuestas' => $this->surveyService->getAll()]));

        return $response->withStatus(200, 'OK');
    }

    public function getById(Request $request, Response $response, $id): Response
    {
        $survey = $this->surveyService->getById((int) $id);

        $response->getBody()->write(json_encode(['encuesta' => $survey]));

        return $response->withStatus(200, 'OK');
    }

    public function create(Request $request, Response $response, $tableId, $orderId): Response
    {
        $body = $request->getParsedBody();
        unset($body[self::ID_KEY]);

        $body[self::TABLE_ID_KEY] = (int) $tableId;
        $body[self::ORDER_ID_KEY] = $orderId;

        $survey = $this->surveyService->createSurvey($body);

        $survey = $this->surveyService->save($survey);

        $response->getBody()->write(json_encode(['encuesta' => $survey]));

        return $response->withStatus(201, 'Creado');
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        if ($this->surveyService->delete((int) $args[self::ID_KEY]) === false) {
            return $response->withStatus(500, 'No se pudo eliminar la encuesta');
        }

        return $response->withStatus(200, 'OK');
    }
}