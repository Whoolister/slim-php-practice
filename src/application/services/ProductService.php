<?php

declare(strict_types=1);

namespace App\application\services;

use App\domain\exceptions\DomainException;
use App\domain\products\Product;
use App\domain\products\ProductRepository;
use App\domain\products\ProductType;
use Exception;
use Psr\Http\Message\UploadedFileInterface;
use function array_shift;
use function count;
use function explode;
use function fclose;
use function fopen;
use function fputcsv;
use function is_numeric;
use function rewind;
use function str_getcsv;
use function stream_get_contents;

class ProductService
{
    private const ID_KEY = 'id';
    private const NAME_KEY = 'nombre';
    private const PRICE_KEY = 'precio';
    private const ESTIMATED_TIME_KEY = 'tiempoEstimado';
    private const TYPE_KEY = 'tipo';
    private const ACTIVE_KEY = 'activo';

    /**
     * Constructs a Product Service.
     *
     * @param ProductRepository $productRepository The product repository to use.
     */
    public function __construct(private ProductRepository $productRepository)
    {
    }

    /**
     * @return Product[] All the products currently persisted.
     */
    public function getAll(): array
    {
        return $this->productRepository->getAll();
    }

    /**
     * Gets all the persisted products of a given type.
     *
     * @param ProductType $type The type of products to get.
     * @return Product[] All the products of the given type currently persisted.
     */
    public function getAllByType(ProductType $type): array
    {
        return $this->productRepository->getAllByType($type);
    }

    /**
     * Gets all the persisted products as a CSV.
     *
     * @return string The CSV representation of all the products currently persisted.
     * @throws DomainException If the CSV could not be obtained.
     */
    public function getAllAsCsv(): string
    {
        $file = fopen('php://temp', 'w');

        fputcsv($file, [self::NAME_KEY, self::PRICE_KEY, self::ESTIMATED_TIME_KEY, self::TYPE_KEY, self::ACTIVE_KEY, self::ID_KEY]);

        foreach ($this->getAll() as $product) {
            fputcsv($file, $product->jsonSerialize());
        }

        rewind($file);

        if (($csv = stream_get_contents($file)) === false) {
            fclose($file);

            throw new DomainException('No se pudo obtener el CSV.', 500);
        }

        fclose($file);

        return $csv;
    }

    /**
     * Gets a product by its ID.
     *
     * @param int $id The ID of the product to get.
     * @return false|Product The product with the given ID, or false if it doesn't exist.
     */
    public function getById(int $id): false|Product
    {
        return $this->productRepository->getById($id);
    }

    /**
     * Persists a product.
     *
     * @param Product $product The product to persist.
     * @return false|Product The created product, or false if it couldn't be created.
     * @throws DomainException If the product could not be saved.
     */
    public function save(Product $product): false|Product
    {
        if (($result = $this->productRepository->save($product)) === false) {
            throw new DomainException('El producto no pudo ser guardado.', 500);
        }

        return $result;
    }

    /**
     * Loads a CSV file of products.
     *
     * @param UploadedFileInterface $file The CSV file to load.
     * @return bool Whether the CSV was loaded successfully.
     */
    public function loadCsv(UploadedFileInterface $file): bool
    {
        $csv = $this->readCsv($file);

        $products = [];

        try {
            // We get rid of the headers
            array_shift($csv);

            foreach ($csv as $row) {
                $elements = count($row);

                if ($elements === 1) {
                    break;
                }

                $product = new Product(
                    name: $row[0],
                    price: (float) $row[1],
                    estimatedTime: (int) $row[2],
                    type: ProductType::tryFrom($row[3]),
                    active: !($elements === 5) || $row[4],
                );

                $products[] = $product;
            }
        } catch (Exception) {
            return false;
        }

        return $this->productRepository->saveMultiple($products);
    }

    /**
     * Deletes a product.
     *
     * @param int $id The ID of the product to delete.
     * @return bool Whether the product was deleted successfully.
     * @throws DomainException If the product does not exist.
     */
    public function delete(int $id): bool
    {
        if (!$this->productRepository->existsById($id)) {
            throw new DomainException('El producto no existe.', 404);
        }

        return $this->productRepository->deleteById($id);
    }

    /**
     * Reads a CSV file and returns an array of its contents
     *
     * @return false|string[][] The contents of the CSV file, or false if the file is not a CSV
     */
    private function readCsv(UploadedFileInterface $file): false|array {
        if ($file->getClientMediaType() !== 'text/csv') {
            return false;
        }

        $stream = $file->getStream();
        $stream->rewind();

        $csv = [];

        foreach (explode("\n", $stream->getContents()) as $line) {
            $csv[] = str_getcsv($line);
        }

        return $csv;
    }

    /**
     * Creates a product from the given data.
     *
     * @param mixed $productData The data to create a product from.
     * @return Product The created product.
     * @throws DomainException If the data is invalid.
     */
    public function createProduct(mixed $productData): Product
    {
        if ($productData === null) {
            throw new DomainException('Los datos del producto no pueden estar vacios.', 400);
        }

        if (!isset($productData[self::NAME_KEY])) {
            throw new DomainException('Falta el nombre del producto.', 400);
        }

        if (!isset($productData[self::PRICE_KEY])) {
            throw new DomainException('Falta el precio del producto.', 400);
        }

        if (!isset($productData[self::ESTIMATED_TIME_KEY])) {
            throw new DomainException('Falta el tiempo estimado de preparacion del producto.', 400);
        }

        if (!isset($productData[self::TYPE_KEY])) {
            throw new DomainException('Falta el tipo del producto.', 400);
        }

        $id = $productData[self::ID_KEY] ?? null;
        $name = $productData[self::NAME_KEY];
        $price = $productData[self::PRICE_KEY];
        $estimatedTime = $productData[self::ESTIMATED_TIME_KEY];
        $type = ProductType::tryFrom($productData[self::TYPE_KEY]);

        if ($id !== null && !is_numeric($id)) {
            throw new DomainException('El ID debe ser un número.', 400);
        }

        if (!is_numeric($price)) {
            throw new DomainException('El precio debe ser un número.', 400);
        }

        if (!is_numeric($estimatedTime)) {
            throw new DomainException('El tiempo estimado de preparacion debe ser un número.', 400);
        }

        if ($type === null) {
            throw new DomainException('El tipo de producto no es válido.', 400);
        }

        return new Product(
            name: $name,
            price: (float) $price,
            estimatedTime: (int) $estimatedTime,
            type: $type,
            id: $id === null ? null : (int) $id
        );
    }
}