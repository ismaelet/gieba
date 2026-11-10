<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardRecap;
use Filament\Widgets\AccountWidget;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getColumns(): int|array
    {
        return [
            'md' => 1,
            'lg' => 2,
        ];
    }

    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            DashboardRecap::class,
        ];
    }
}
