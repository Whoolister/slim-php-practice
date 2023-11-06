<?php
declare(strict_types=1);

namespace App\controllers;

use App\repositories\SurveyRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotImplementedException;

readonly class SurveyController
{
    public function __construct(private SurveyRepository $surveyRepository)
    {
    }

    public function GetAll(Request $request, Response $response, array $args): Response
    {
        throw new HttpNotImplementedException($request, "This endpoint hasn't been implemented yet");
    }

    public function GetOne(Request $request, Response $response, array $args): Response
    {
        throw new HttpNotImplementedException($request, "This endpoint hasn't been implemented yet");
    }

    public function Add(Request $request, Response $response, array $args): Response
    {
        throw new HttpNotImplementedException($request, "This endpoint hasn't been implemented yet");
    }

    public function Update(Request $request, Response $response, array $args): Response
    {
        throw new HttpNotImplementedException($request, "This endpoint hasn't been implemented yet");
    }

    public function Delete(Request $request, Response $response, array $args): Response
    {
        throw new HttpNotImplementedException($request, "This endpoint hasn't been implemented yet");
    }
}