<?php

declare(strict_types=1);

namespace App\domain\products;

use App\domain\users\UserRole;
use function strtoupper;

/**
 * Types of Product that are sold in the Restaurant, each being in charge of a different type of staff.
 *
 * @see Product for more information about the Product entity
 * @see UserRole for more information about the different types of staff
 */
enum ProductType: string
{
    /**
     * Wine or Alcoholic Drinks in general
     */
    case WINE_OR_DRINK = 'TRAGO O VINO';

    /**
     * Artisanal Beer
     */
    case BEER = 'CERVEZA';

    /**
     * Food made in the Kitchen
     */
    case MEAL = 'PLATILLO';

    /**
     * Food made in the Candy Bar
     */
    case PASTRIES = 'POSTRE';

    /**
     * @param string $productType The string to convert to a Product Type.
     * @return ?ProductType The Product Type that corresponds to the given string, or null if it doesn't exist.
     */
    public static function tryFromIgnoringCase(string $productType): ?ProductType
    {
        return self::tryFrom(strtoupper($productType));
    }
}
