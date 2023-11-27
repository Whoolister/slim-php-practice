<?php

declare(strict_types=1);

namespace App\domain\users;

use function strtoupper;

/**
 * Roles that a user can have, with each role having a different set of permissions and responsibilities.
 *
 * @see User for more information about the user entity
 */
enum UserRole: string
{
    /**
     * User in charge of preparing drinks or serving wine
     */
    case BARTENDER = 'BARTENDER';

    /**
     * User in charge of serving beer
     */
    case BREWER = 'CERVECERO';

    /**
     * User in charge of preparing meals
     */
    case CHEF = 'COCINERO';

    /**
     * User in charge of preparing pastries
     */
    case BAKER = 'PASTELERO';

    /**
     * User in charge of taking and delivering orders
     */
    case WAITER = 'MOZO';

    /**
     * User with the highest level of permissions, in charge of managing the restaurant
     */
    case PARTNER = 'SOCIO';

    /**
     * @param string $userRole The string to convert to a User Role.
     * @return ?UserRole The UserRole that corresponds to the given string, or null if it doesn't exist.
     */
    public static function tryFromIgnoringCase(string $userRole): ?UserRole
    {
        return self::tryFrom(strtoupper($userRole));
    }
}
