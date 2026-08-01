<?php

declare(strict_types=1);

namespace App\Enums;

enum ItemStatus: string
{
    case Available = 'tersedia';
    case NotAvailable = 'tidak tersedia';
}
