<?php

declare(strict_types=1);

namespace App\entities\orders;

enum OrderItemStatus: string
{
    case PENDING = 'PENDIENTE';
    case PREPARING = 'PREPARANDO';
    case READY = 'LISTO';
}
