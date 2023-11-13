<?php
declare(strict_types=1);

namespace App\services;

use App\entities\orders\Survey;
use App\entities\products\Product;
use App\repositories\orders\SurveyRepository;

final readonly class SurveyService
{
    public function __construct(private SurveyRepository $surveyRepository)
    {
    }

    public function getAll(): array
    {
        return $this->surveyRepository->getAll();
    }

    public function getAllByOrderId(int $orderId): array
    {
        return $this->surveyRepository->getAllByOrderId($orderId);
    }

    public function getOne(int $id): false|Survey
    {
        return $this->surveyRepository->getById($id);
    }

    public function getOneByOrderId(int $id, int $orderId): false|Survey
    {
        return $this->surveyRepository->getByIdAndOrderId($id, $orderId);
    }

    public function add(Survey $survey): false|Survey
    {
        if ($survey->getId() !== null) {
            return false;
        }

        return $this->surveyRepository->save($survey);
    }

    public function update(Survey $survey): false|Survey
    {
        if (!$this->surveyRepository->existsById($survey->getId())) {
            return false;
        }

        return $this->surveyRepository->save($survey);
    }

    public function delete(int $id): bool
    {
        return $this->surveyRepository->deleteById($id);
    }

    public function deleteByOrderId(int $id, int $orderId): bool
    {
        return $this->surveyRepository->deleteByIdAndOrderId($id, $orderId);
    }
}