<?php

declare(strict_types=1);

namespace App\controllers;

use App\entities\products\Product;
use App\entities\products\ProductType;
use App\services\ProductService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function is_numeric;
use function json_encode;

final readonly class ProductController
{
    private const ID_KEY = 'idProducto';
    private const NAME_KEY = 'nombre';
    private const PRICE_KEY = 'precio';
    private const ESTIMATED_TIME_KEY = 'tiempo_estimado';
    private const TYPE_KEY = 'tipo';

    /**
     * Constructs a Product Controller
     *
     * @param ProductService $productService The product service to use
     */
    public function __construct(private ProductService $productService)
    {
    }

    public function getAll(Request $request, Response $response, array $args): Response
    {
        $response->getBody()->write(json_encode($this->productService->getAll()));

        return $response->withStatus(200, 'OK');
    }

    public function GetOne(Request $request, Response $response, array $args): Response
    {
        if (($id = $args[self::ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (!is_numeric($id)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        if (($product = $this->productService->GetOne((int) $id)) === false) {
            return $response->withStatus(404, 'No se encontró el producto');
        }

        $response->getBody()->write(json_encode($product));

        return $response->withStatus(200, 'OK');
    }

    public function add(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();

        if (($name = $body[self::NAME_KEY]) === null) {
            return $response->withStatus(400, 'Falta el nombre');
        }

        if (($price = $body[self::PRICE_KEY]) === null) {
            return $response->withStatus(400, 'Falta el precio');
        }

        if (!is_numeric($price)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        if (($estimatedTime = $body[self::ESTIMATED_TIME_KEY]) === null) {
            return $response->withStatus(400, 'Falta el tiempo estimado de preparación');
        }

        if (!is_numeric($estimatedTime)) {
            return $response->withStatus(400, 'El tiempo estimado de preparación debe ser un número');
        }

        if (($type = $body[self::TYPE_KEY]) === null) {
            return $response->withStatus(400, 'Falta el tipo');
        }

        if (($type = ProductType::tryFrom($type)) === null) {
            return $response->withStatus(400, 'El tipo no es válido');
        }

        if (($result = $this->productService->add(new Product($name, (int) $price, (int) $estimatedTime, $type))) === false) {
            return $response->withStatus(500, 'No se pudo agregar el producto');
        }

        $response->getBody()->write(json_encode($result));

        return $response->withStatus(201, 'Creado');
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $body = $request->getParsedBody();

        if (($id = $args[self::ID_KEY]) === null) {
            return $response->withStatus(400, 'Falta el ID');
        }

        if (!is_numeric($id)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        if (($name = $body[self::NAME_KEY]) === null) {
            return $response->withStatus(400, 'Falta el nombre');
        }

        if (($price = $body[self::PRICE_KEY]) === null) {
            return $response->withStatus(400, 'Falta el precio');
        }

        if (!is_numeric($price)) {
            return $response->withStatus(400, 'El ID debe ser un número');
        }

        if (($estimatedTime = $body[self::ESTIMATED_TIME_KEY]) === null) {
            return $response->withStatus(400, 'Falta el tiempo estimado de preparación');
        }

        if (!is_numeric($estimatedTime)) {
            return $response->withStatus(400, 'El tiempo estimado de preparación debe ser un número');
        }

        if (($type = $body[self::TYPE_KEY]) === null) {
            return $response->withStatus(400, 'Falta el tipo');
        }

        if (($type = ProductType::tryFrom($type)) === null) {
            return $response->withStatus(400, 'El tipo no es válido');
        }

        if (($result = $this->productService->update(new Product($name, (float) $price, (int) $estimatedTime, $type, id: (int) $id))) === false) {
            return $response->withStatus(500, 'No se pudo actualizar el producto');
        }

        $response->getBody()->write(json_encode($result));
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

        if ($this->productService->delete((int) $id) === false) {
            return $response->withStatus(500, 'No se pudo eliminar el producto');
        }

        return $response->withStatus(200, 'OK');
    }
}