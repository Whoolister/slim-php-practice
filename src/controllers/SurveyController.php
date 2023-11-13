<?php
declare(strict_types=1);

namespace App\controllers;

use App\entities\orders\Survey;
use App\services\SurveyService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function is_numeric;
use function json_encode;

final readonly class SurveyController
{
    private const ID_KEY = 'idEncuesta';
    private const ORDER_ID_KEY = 'idPedido';
    private const TABLE_RATING_KEY = 'puntuacionMesa';
    private const RESTAURANT_RATING_KEY = 'puntuacionRestaurante';
    private const WAITER_RATING_KEY = 'puntuacionCamarero';
    private const CHEF_RATING_KEY = 'puntuacionCocinero';
    private const COMMENT_KEY = 'comentario';

    public function __construct(private SurveyService $surveyService)
    {
    }

    public function getAll(Request $request, Response $response, array $args): Response
    {
        if (($orderId = $args[self::ORDER_ID_KEY]) === null) {
            $response->getBody()->write(json_encode($this->surveyService->getAll()));

            return $response->withStatus(200, 'OK');
        }

        if (!is_numeric($orderId)) {
            return $response->withStatus(400, 'El ID del pedido debe ser un número');
        }

        $response->getBody()->write(json_encode($this->surveyService->getAllByOrderId((int) $orderId)));

        return $response->withStatus(200, 'OK');
    }

    public function getOne(Request $request, Response $response, array $args): Response
    {
        if (($id = $args[self::ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (!is_numeric($id)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        if (($orderId = $args[self::ORDER_ID_KEY]) === null) {
            if (($survey = $this->surveyService->getOne((int) $id)) === false) {
                return $response->withStatus(404, 'No se encontró la encuesta');
            }

            $response->getBody()->write(json_encode($survey));

            return $response->withStatus(200, 'OK');
        }

        if (!is_numeric($orderId)) {
            return $response->withStatus(400, 'El ID del pedido debe ser un número');
        }

        if (($survey = $this->surveyService->getOneByOrderId((int) $id, (int) $orderId)) === false) {
            return $response->withStatus(404, 'No se encontró la encuesta');
        }

        $response->getBody()->write(json_encode($survey));

        return $response->withStatus(200, 'OK');
    }

    public function add(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();

        if (($orderId = $args[self::ORDER_ID_KEY] ?? $body[self::ORDER_ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID del pedido');
        }

        if (!is_numeric($orderId)) {
            return $response->withStatus(400, 'El ID del pedido debe ser un número');
        }

        if (($tableRating = $body[self::TABLE_RATING_KEY]) === null) {
            return $response->withStatus(400, 'Falta la puntuación de la mesa');
        }

        if (!is_numeric($tableRating)) {
            return $response->withStatus(400, 'La puntuación de la mesa debe ser un número');
        }

        if (($restaurantRating = $body[self::RESTAURANT_RATING_KEY]) === null) {
            return $response->withStatus(400, 'Falta la puntuación del restaurante');
        }

        if (!is_numeric($restaurantRating)) {
            return $response->withStatus(400, 'La puntuación del restaurante debe ser un número');
        }

        if (($waiterRating = $body[self::WAITER_RATING_KEY]) === null) {
            return $response->withStatus(400, 'Falta la puntuación del camarero');
        }

        if (!is_numeric($waiterRating)) {
            return $response->withStatus(400, 'La puntuación del camarero debe ser un número');
        }

        if (($chefRating = $body[self::CHEF_RATING_KEY]) === null) {
            return $response->withStatus(400, 'Falta la puntuación del cocinero');
        }

        if (!is_numeric($chefRating)) {
            return $response->withStatus(400, 'La puntuación del cocinero debe ser un número');
        }

        $comment = $body[self::COMMENT_KEY] ?? null;

        if (($survey = $this->surveyService->add(new Survey((int)$orderId, (int) $tableRating, (int) $restaurantRating, (int) $waiterRating, (int) $chefRating, $comment))) === false) {
            return $response->withStatus(500, 'No se pudo agregar la encuesta');
        }

        $response->getBody()->write(json_encode($survey));

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

        if (($orderId = $args[self::ORDER_ID_KEY] ?? $body[self::ORDER_ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID del pedido');
        }

        if (!is_numeric($orderId)) {
            return $response->withStatus(400, 'El ID del pedido debe ser un número');
        }

        if (($tableRating = $body[self::TABLE_RATING_KEY]) === null) {
            return $response->withStatus(400, 'Falta la puntuación de la mesa');
        }

        if (!is_numeric($tableRating)) {
            return $response->withStatus(400, 'La puntuación de la mesa debe ser un número');
        }

        if (($restaurantRating = $body[self::RESTAURANT_RATING_KEY]) === null) {
            return $response->withStatus(400, 'Falta la puntuación del restaurante');
        }

        if (!is_numeric($restaurantRating)) {
            return $response->withStatus(400, 'La puntuación del restaurante debe ser un número');
        }

        if (($waiterRating = $body[self::WAITER_RATING_KEY]) === null) {
            return $response->withStatus(400, 'Falta la puntuación del camarero');
        }

        if (!is_numeric($waiterRating)) {
            return $response->withStatus(400, 'La puntuación del camarero debe ser un número');
        }

        if (($chefRating = $body[self::CHEF_RATING_KEY]) === null) {
            return $response->withStatus(400, 'Falta la puntuación del cocinero');
        }

        if (!is_numeric($chefRating)) {
            return $response->withStatus(400, 'La puntuación del cocinero debe ser un número');
        }

        $comment = $body[self::COMMENT_KEY] ?? null;

        if (($survey = $this->surveyService->update(new Survey((int)$orderId, (int) $tableRating, (int) $restaurantRating, (int) $waiterRating, (int) $chefRating, $comment, $id))) === false) {
            return $response->withStatus(500, 'No se pudo actualizar la encuesta');
        }

        $response->getBody()->write(json_encode($survey));

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

        if (($orderId = $args[self::ORDER_ID_KEY]) === null) {
            if ($this->surveyService->delete((int) $id) === false) {
                return $response->withStatus(500, 'No se pudo eliminar la encuesta');
            }

            return $response->withStatus(200, 'OK');
        }

        if (!is_numeric($orderId)) {
            return $response->withStatus(400, 'El ID del pedido debe ser un número');
        }

        if ($this->surveyService->deleteByOrderId((int) $id, (int) $orderId) === false) {
            return $response->withStatus(500, 'No se pudo eliminar la encuesta');
        }

        return $response->withStatus(200, 'OK');
    }
}