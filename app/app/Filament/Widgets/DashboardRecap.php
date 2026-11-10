<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Widgets\Widget;
use Illuminate\Support\Str;

class DashboardRecap extends Widget
{
    /**
     * Property: the column span of the widget
     */
    protected int|string|array $columnSpan = 'full';

    /**
     * Property: the view for the widget
     */
    protected string $view = 'filament.widgets.dashboard-recap';

    /**
     * Get the status recap data
     */
    public function statusData(): array
    {
        $recapData = [];
        $statuses = config('ecommerce.order_statuses');

        foreach ($statuses as $statusId => $status) {
            $statusData = [];

            $statusData['title'] = $status['label'];
            $statusData['value'] = OrderItem::where('status', $statusId)->sum('total_price_gross');
            $statusData['slug'] = $status['tag'] ?? Str::slug($status['label']);

            $recapData[$statusId] = $statusData;
        }

        return $recapData;
    }

    /**
     * Get the total billed withdrawn amount
     */
    public function billedWithdrawn(): float
    {
        return OrderItem::whereHas('order', fn ($query) => $query->where('billed', true))
            ->where('status', 6)
            ->sum('total_price_gross');
    }

    /**
     * Get the total not billed withdrawn amount
     */
    public function notBilledWithdrawn(): float
    {
        return OrderItem::whereHas('order', fn ($query) => $query->where('billed', false))
            ->where('status', 6)
            ->sum('total_price_gross');
    }
}
