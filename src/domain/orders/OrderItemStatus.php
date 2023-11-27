<?php

declare(strict_types=1);

namespace App\domain\orders;

enum OrderItemStatus: string
{
    case PENDING = 'PENDIENTE';
    case PREPARING = 'PREPARANDO';
    case READY = 'LISTO';
}
