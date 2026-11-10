<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Action as ModelsAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditOrder extends EditRecord
{
    use HandleOrderPageLogic;

    /**
     * Property: The resource class associated with this page
     */
    protected static string $resource = OrderResource::class;

    /**
     * Property: Store the original status of the order for comparison after saving
     */
    protected string $originalStatus;

    /**
     * Property: Store the original quantity of the order for comparison after saving
     */
    protected int $originalQuantity = 0;

    /**
     * Property: Store the original gross price of the order for comparison after saving
     */
    protected float $originalPriceGross = 0.0;

    /**
     * Filament Method: Get the header actions for the edit page
     */
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('duplicate')
                ->label('Duplica ordine')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->requiresConfirmation()
                ->action(fn () => $this->duplicateRecord()),
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
     * Filament Method: Mutate the form data before saving the record, storing original values for comparison
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->originalStatus = $this->record->statusName();
        $this->originalQuantity = $this->record->quantity;
        $this->originalPriceGross = $this->record->price_gross;

        return $data;
    }

    /**
     * Filament Method: Actions to perform after saving the record
     */
    protected function afterSave(): void
    {
        // Update the order's totals
        $this->updateOrder();

        // Log the action with details about the changes made
        $this->logAction();

        // Redirect back to the previous page or a specific URL after saving
        $this->redirect(
            $this->getReturnUrlFromSession()
        );
    }

    /**
     * Custom Method: Log the action of editing the order, including details about what was changed
     */
    protected function logAction(): void
    {
        // Generate a log message based on the changes made to the order
        $actionTextParts = [];

        if ($this->originalStatus !== $this->record->statusName()) {
            $actionTextParts[] = " cambiando lo stato da '{$this->originalStatus}' a '{$this->record->statusName()}'";
        }

        if ($this->originalQuantity !== $this->record->quantity) {
            $actionTextParts[] = " cambiando la quantità di prodotti da {$this->originalQuantity} a {$this->record->quantity}";
        }

        if ($this->originalPriceGross !== $this->record->price_gross) {
            $actionTextParts[] = ' cambiando il prezzo totale da '.price($this->originalPriceGross).' a '.price($this->record->price_gross);
        }

        if (empty($actionTextParts)) {
            return;
        }

        $baseActionText = "Ha modificato l'ordine #{$this->record->id}";
        $actionText = $baseActionText.' '.implode(' e ', $actionTextParts);

        // Log the action in the database
        ModelsAction::create([
            'user_id' => Auth::id(),
            'action' => $actionText,
        ]);
    }

    /**
     * Custom Method: Duplicate the current order record along with its associated order items, and redirect to the edit page of the new copy
     */
    protected function duplicateRecord(): void
    {
        DB::transaction(function () {
            $original = $this->record->load('order_items');

            $copy = $original->replicate([
                'id',
                'created_at',
                'updated_at',
                'status',
                'billed',
                'total_discount',
                'down_payment',
                'down_payment_with_vat',
                'fixed_discount',
                'discount_with_vat',
                'percentage_discount',
            ]);

            $copy->created_at = now();
            $copy->save();

            foreach ($original->order_items as $item) {
                $copy->order_items()->create(
                    $item->replicate([
                        'id',
                        'order_id',
                        'status',
                        'created_at',
                        'updated_at',
                    ])->toArray()
                );
            }

            $this->redirect(
                static::getResource()::getUrl('edit', ['record' => $copy])
            );
        });
    }
}
