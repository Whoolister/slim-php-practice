<?php
declare(strict_types=1);

namespace App\application\services;

use Exception;
use Psr\Http\Message\UploadedFileInterface;
use function file_exists;
use function mkdir;

/**
 * Service for handling actions related to Images.
 */
class ImageService
{
    /**
     * Saves an image in the specified path.
     *
     * @param UploadedFileInterface $image The image to be saved.
     * @param string $path The path where the image will be saved.
     * @param string $name The name of the image, without the extension.
     * @return bool True if the image was saved successfully, false otherwise.
     */
    public function save(UploadedFileInterface $image, string $path, string $name): bool
    {
        if ($image->getError() !== UPLOAD_ERR_OK) {
            return false;
        }

        if (!file_exists($path)) {
            mkdir($path, recursive: true);
        }

        try {
            $image->moveTo($path . $name . '.jpg');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Moves an image from one path to another.
     *
     * @param string $source The path where the image is located.
     * @param string $destination The path where the image will be moved.
     * @param string $fileName The name of the file to be moved.
     * @return bool True if the image was moved successfully, false otherwise.
     */
    public function move(string $source, string $destination, string $fileName): bool
    {
        if (!file_exists($source . $fileName)) {
            return false;
        }

        if (!file_exists($destination)) {
            mkdir($destination, recursive: true);
        }

        return rename($source, $destination . $fileName);
    }
}