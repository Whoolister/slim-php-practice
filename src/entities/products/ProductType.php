<?php

declare(strict_types=1);

namespace App\entities\products;

use App\entities\users\UserRole;

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
}
