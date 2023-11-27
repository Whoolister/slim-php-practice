<?php

declare(strict_types=1);

namespace App\domain\orders;

use function strtoupper;

/**
 * States that an Order can be in.
 */
enum OrderStatus: string
{
    /**
     * The order has been created, but no action has been taken on it.
     */
    case PENDING = 'PENDIENTE';

    /**
     * At least one item has started preparation.
     */
    case PREPARING = 'PREPARANDO';

    /**
     * All items have been prepared.
     */
    case READY = 'LISTO';

    /**
     * All items have been served.
     */
    case SERVED = 'SERVIDO';

    /**
     * The order has been paid.
     */
    case PAID = 'PAGADO';

    /**
     * @param string $orderStatus The order status to convert to an Order Status.
     * @return ?OrderStatus The Order Status that corresponds to the given string, or null if it doesn't exist.
     */
    public static function tryFromIgnoringCase(string $orderStatus): ?OrderStatus
    {
        return self::tryFrom(strtoupper($orderStatus));
    }
}
