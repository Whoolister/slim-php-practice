<?php

declare(strict_types=1);

namespace App\entities\tables;

/**
 * Status of a Table, which can be used to determine what the customers are doing.
 *
 * @see Table
 */
enum TableStatus: string
{
    /**
     * Table has customers, and they're waiting for their order
     */
    case WAITING_FOR_ORDER = 'ESPERANDO';

    /**
     * Table has customers, and they have ordered
     */
    case EATING = 'COMIENDO';

    /**
     * Table has customers, and they have asked for the bill
     */
    case PAYING = 'PAGANDO';

    /**
     * Table has no customers
     */
    case CLOSED = 'CERRADA';
}
