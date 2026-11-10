<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateOrder extends CreateRecord
{
    use HandleOrderPageLogic;

    /**
     * Property: The resource class associated with this page
     */
    protected static string $resource = OrderResource::class;

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
        $this->updateOrder();

        Action::create([
            'user_id' => Auth::id(),
            'action' => "Ha creato l'ordine #{$this->record->id}",
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getReturnUrlFromSession();
    }
}
