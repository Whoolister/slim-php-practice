<?php

declare(strict_types=1);

namespace App\controllers;

use App\entities\products\ProductType;
use App\services\ProductService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function filter_var;
use function is_numeric;
use function json_encode;
use const FILTER_NULL_ON_FAILURE;

readonly class ProductController
{
    /**
     * Constructs a Product Controller
     *
     * @param ProductService $productService The product service to use
     */
    public function __construct(private ProductService $productService)
    {
    }

    public function GetAll(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(json_encode($this->productService->GetAll()));

        return $response->withStatus(200, 'OK');
    }

    public function GetOne(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        if (($id = $args['id']) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (!is_numeric($id)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        if (($product = $this->productService->GetById((int) $id)) === false) {
            return $response->withStatus(404, 'No se encontró el producto');
        }

        $response->getBody()->write(json_encode($product));

        return $response->withStatus(200, 'OK');
    }

    public function Add(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        $body = $request->getParsedBody();

        if (($name = $body['nombre']) === null) {
            return $response->withStatus(400, 'Falta el nombre');
        }

        if (($price = $body['precio']) === null) {
            return $response->withStatus(400, 'Falta el precio');
        }

        if (!is_numeric($price)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        if (($estimatedTime = $body['tiempo_estimado']) === null) {
            return $response->withStatus(400, 'Falta el tiempo estimado de preparación');
        }

        if (!is_numeric($estimatedTime)) {
            return $response->withStatus(400, 'El tiempo estimado de preparación debe ser un número');
        }

        if (($type = $body['tipo']) === null) {
            return $response->withStatus(400, 'Falta el tipo');
        }

        if (($type = ProductType::tryFrom($type)) === null) {
            return $response->withStatus(400, 'El tipo no es válido');
        }

        if (($result = $this->productService->Add($name, (int) $price, (int) $estimatedTime, $type)) === false) {
            return $response->withStatus(500, 'No se pudo agregar el producto');
        }

        $response->getBody()->write(json_encode($result));

        return $response->withStatus(201, 'Creado');
    }

    public function Update(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        if (($id = $args['id']) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (!is_numeric($id)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        $body = $request->getParsedBody();

        if (($name = $body['nombre']) === null) {
            return $response->withStatus(400, 'Falta el nombre');
        }

        if (($price = $body['precio']) === null) {
            return $response->withStatus(400, 'Falta el precio');
        }

        if (!is_numeric($price)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        if (($estimatedTime = $body['tiempo_estimado']) === null) {
            return $response->withStatus(400, 'Falta el tiempo estimado de preparación');
        }

        if (!is_numeric($estimatedTime)) {
            return $response->withStatus(400, 'El tiempo estimado de preparación debe ser un número');
        }

        if (($type = $body['tipo']) === null) {
            return $response->withStatus(400, 'Falta el tipo');
        }

        if (($type = ProductType::tryFrom($type)) === null) {
            return $response->withStatus(400, 'El tipo no es válido');
        }

        if (($active = $body['activo']) === null) {
            return $response->withStatus(400, 'Falta el estado');
        }

        if (($active = filter_var($active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) === null) {
            return $response->withStatus(400, 'El estado no es válido');
        }

        if (($result = $this->productService->Update((int) $id, $name, (float) $price, (int) $estimatedTime, $type, $active)) === false) {
            return $response->withStatus(500, 'No se pudo actualizar el producto');
        }

        $response->getBody()->write(json_encode($result));
        return $response->withStatus(200, 'OK');
    }

    public function Delete(Request $request, Response $response, array $args): Response
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        if (($id = $args['id']) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (!is_numeric($id)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        if ($this->productService->Delete((int) $id) === false) {
            return $response->withStatus(500, 'No se pudo eliminar el producto');
        }

        return $response->withStatus(200, 'OK');
    }
}