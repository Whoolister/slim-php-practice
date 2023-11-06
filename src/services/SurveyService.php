<?php
declare(strict_types=1);

namespace App\services;

use App\repositories\SurveyRepository;
use Psr\Http\Message\ResponseInterface as Response;

readonly class SurveyService
{
    public function __construct(private SurveyRepository $surveyRepository)
    {
    }

    public function GetAll(): Response
    {
        // TODO: Implement GetAll() method.
    }

    public function GetOne(): Response
    {
        // TODO: Implement GetAll() method.
    }

    public function Add(): Response
    {
        // TODO: Implement GetAll() method.
    }

    public function Update(): Response
    {
        // TODO: Implement GetAll() method.
    }

    public function Delete(): Response
    {
        // TODO: Implement GetAll() method.
    }
}