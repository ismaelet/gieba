<?php

namespace App\Filament\Resources\SupplierOrders\Pages;

use App\Filament\Resources\SupplierOrders\SupplierOrderResource;
use App\Models\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSupplierOrder extends CreateRecord
{
    use HandleSupplierOrderPageLogic;

    protected static string $resource = SupplierOrderResource::class;

    /**
     * Filament Method: Mount the edit page with the given record
     */
    public function mount(): void
    {
        $this->storeReturnUrlInSession();

        parent::mount();
    }

    protected function afterCreate(): void
    {
        Action::create([
            'user_id' => Auth::id(),
            'action' => "Ha creato il carrello del fornitore {$this->record->supplier->name} (#{$this->record->id})",
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getReturnUrlFromSession();
    }
}
