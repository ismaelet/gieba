<?php

namespace App\Filament\Resources\SupplierOrders\Pages;

use App\Filament\Resources\SupplierOrders\SupplierOrderResource;
use App\Models\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditSupplierOrder extends EditRecord
{
    use HandleSupplierOrderPageLogic;

    /**
     * Property: The resource class associated with this page
     */
    protected static string $resource = SupplierOrderResource::class;

    /**
     * Filament Method: Get the header actions for the edit page
     */
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Filament Method: Mount the edit page with the given record
     */
    public function mount($record): void
    {
        $this->storeReturnUrlInSession();

        parent::mount($record);
    }

    /**
     * Filament Method: Actions to perform after saving the record
     */
    protected function afterSave(): void
    {
        $this->redirect(
            $this->getReturnUrlFromSession()
        );

        Action::create([
            'user_id' => Auth::id(),
            'action' => "Ha modificato il carrello del fornitore {$this->record->supplier->name} (#{$this->record->id})",
        ]);
    }
}
