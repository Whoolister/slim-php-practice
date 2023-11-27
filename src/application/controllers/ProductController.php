<?php

declare(strict_types=1);

namespace App\application\controllers;

use App\application\services\ProductService;
use App\domain\products\ProductType;
use App\domain\users\UserRole;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function json_encode;

class ProductController
{
    private const ID_KEY = 'id';
    private const FILE_KEY = 'archivo';

    private ProductService $productService;

    /**
     * Constructs a Product Controller
     *
     * @param ProductService $productService The product service to use
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getAll(Request $request, Response $response): Response
    {
        $role = $request->getAttribute(UserRole::class);

        $products = match ($role) {
            UserRole::BARTENDER => $this->productService->getAllByType(ProductType::WINE_OR_DRINK),
            UserRole::BREWER => $this->productService->getAllByType(ProductType::BEER),
            UserRole::CHEF => $this->productService->getAllByType(ProductType::MEAL),
            UserRole::BAKER => $this->productService->getAllByType(ProductType::PASTRIES),
            UserRole::PARTNER, UserRole::WAITER => $this->productService->getAll(),
            default => [],
        };

        $response->getBody()->write(json_encode(['productos' => $products]));

        return $response->withStatus(200, 'OK');
    }

    public function getAsCsv(Request $request, Response $response): Response
    {
        $products = $this->productService->getAllAsCsv();

        $response->getBody()->write($products);

        return $response->withHeader('Content-Type', 'text/csv')->withStatus(200, 'OK');
    }

    public function getById(Request $request, Response $response, $id): Response
    {
        $product = $this->productService->getById((int) $id);

        if ($product === false) {
            return $response->withStatus(404, 'No se encontró el producto');
        }

        $response->getBody()->write(json_encode(['producto' => $product]));

        return $response->withStatus(200, 'OK');
    }

    public function create(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();
        unset($body[self::ID_KEY]);

        $product = $this->productService->createProduct($body);

        $result = $this->productService->save($product);

        $response->getBody()->write(json_encode(['producto' =>$result]));

        return $response->withStatus(201, 'Creado');
    }

    public function addAsCsv(Request $request, Response $response): Response
    {
        $uploadedFiles = $request->getUploadedFiles();

        if (!isset($uploadedFiles[self::FILE_KEY])) {
            return $response->withStatus(400, 'Falta el archivo');
        }

        $file = $uploadedFiles[self::FILE_KEY];

        if (!$this->productService->loadCsv($file)) {
            return $response->withStatus(500, 'No se pudieron agregar todos los productos');
        }

        return $response->withStatus(201, 'Creado');
    }

    public function update(Request $request, Response $response, $id): Response
    {
        $body = $request->getParsedBody();
        $body[self::ID_KEY] = $id;

        $product = $this->productService->createProduct($body);

        $product = $this->productService->save($product);

        $response->getBody()->write(json_encode(['producto' => $product]));

        return $response->withStatus(200, 'OK');
    }

    public function delete(Request $request, Response $response, $id): Response
    {
        if (!$this->productService->delete((int) $id)) {
            return $response->withStatus(500, 'No se pudo eliminar el producto');
        }

        return $response->withStatus(200, 'OK');
    }
}