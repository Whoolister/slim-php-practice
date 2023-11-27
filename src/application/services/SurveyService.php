<?php
declare(strict_types=1);

namespace App\application\services;

use App\domain\exceptions\DomainException;
use App\domain\surveys\Survey;
use App\domain\surveys\SurveyRepository;
use function preg_match;

class SurveyService
{
    private const ID_KEY = 'id';
    private const ORDER_ID_KEY = 'idPedido';
    private const TABLE_RATING_KEY = 'puntuacionMesa';
    private const RESTAURANT_RATING_KEY = 'puntuacionRestaurante';
    private const WAITER_RATING_KEY = 'puntuacionCamarero';
    private const CHEF_RATING_KEY = 'puntuacionCocinero';
    private const COMMENT_KEY = 'comentario';

    private SurveyRepository $surveyRepository;

    public function __construct(SurveyRepository $surveyRepository)
    {
        $this->surveyRepository = $surveyRepository;
    }

    public function getAll(): array
    {
        return $this->surveyRepository->getAll();
    }

    public function getBest(): array
    {
        return $this->surveyRepository->getBest();
    }

    public function getById(int $id): false|Survey
    {
        return $this->surveyRepository->getById($id);
    }

    public function save(Survey $survey): Survey
    {
        if (($result = $this->surveyRepository->save($survey)) === false) {
            throw new DomainException('No se pudo guardar la encuesta', 500);
        }

        return $result;
    }

    public function delete(int $id): bool
    {
        if ($this->surveyRepository->existsById($id) === false) {
            throw new DomainException('No se encontró la encuesta', 404);
        }

        return $this->surveyRepository->deleteById($id);
    }

    public function createSurvey(mixed $surveyData): Survey {
        if ($surveyData === null) {
            throw new DomainException('Los datos de la encuesta no pueden estar vacios.', 400);
        }

        if (!isset($surveyData[self::ORDER_ID_KEY])) {
            throw new DomainException('Falta el ID del pedido.', 400);
        }

        if (!isset($surveyData[self::TABLE_RATING_KEY])) {
            throw new DomainException('Falta la puntuación de la mesa.', 400);
        }

        if (!isset($surveyData[self::RESTAURANT_RATING_KEY])) {
            throw new DomainException('Falta la puntuación del restaurante.', 400);
        }

        if (!isset($surveyData[self::WAITER_RATING_KEY])) {
            throw new DomainException('Falta la puntuación del camarero.', 400);
        }

        if (!isset($surveyData[self::CHEF_RATING_KEY])) {
            throw new DomainException('Falta la puntuación del cocinero.', 400);
        }

        $id = $surveyData[self::ID_KEY] ?? null;
        $orderId = $surveyData[self::ORDER_ID_KEY];
        $tableRating = $surveyData[self::TABLE_RATING_KEY];
        $restaurantRating = $surveyData[self::RESTAURANT_RATING_KEY];
        $waiterRating = $surveyData[self::WAITER_RATING_KEY];
        $chefRating = $surveyData[self::CHEF_RATING_KEY];
        $comment = $surveyData[self::COMMENT_KEY] ?? null;

        if ($id !== null && !is_numeric($id)) {
            throw new DomainException('El ID debe ser un número.', 400);
        }

        if (preg_match('/^\w{5}$/', $orderId) === false) {
            throw new DomainException('El ID del pedido debe ser un codigo alfanumerico de 5 digitos.', 400);
        }

        if (!is_numeric($tableRating)) {
            throw new DomainException('La puntuación de la mesa debe ser un número.', 400);
        }

        if (!is_numeric($restaurantRating)) {
            throw new DomainException('La puntuación del restaurante debe ser un número.', 400);
        }

        if (!is_numeric($waiterRating)) {
            throw new DomainException('La puntuación del camarero debe ser un número.', 400);
        }

        if (!is_numeric($chefRating)) {
            throw new DomainException('La puntuación del cocinero debe ser un número.', 400);
        }

        $tableRating = (int) $tableRating;
        $restaurantRating = (int) $restaurantRating;
        $waiterRating = (int) $waiterRating;
        $chefRating = (int) $chefRating;

        if ($tableRating <= 0 || $tableRating > 10) {
            throw new DomainException('La puntuación de la mesa debe estar entre 1 y 10.', 400);
        }

        if ($restaurantRating <= 0 || $restaurantRating > 10) {
            throw new DomainException('La puntuación del restaurante debe estar entre 1 y 10.', 400);
        }

        if ($waiterRating <= 0 || $waiterRating > 10) {
            throw new DomainException('La puntuación del camarero debe estar entre 1 y 10.', 400);
        }

        if ($chefRating <= 0 || $chefRating > 10) {
            throw new DomainException('La puntuación del cocinero debe estar entre 1 y 10.', 400);
        }

        if ($comment !== null && empty($comment)) {
            throw new DomainException('El comentario no puede estar vacio.', 400);
        }

        return new Survey(
            orderId: $orderId,
            tableRating: $tableRating,
            restaurantRating: $restaurantRating,
            waiterRating: $waiterRating,
            chefRating: $chefRating,
            comment: $comment,
            id: $id === null ? null : (int) $id,
        );
    }
}