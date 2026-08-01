<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\AccountWidget as BaseAccountWidget;

final class AccountWidget extends BaseAccountWidget
{
    protected int|string|array $columnSpan = 'full';
}
