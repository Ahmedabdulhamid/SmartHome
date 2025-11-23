<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use App\Models\AdditionalCost;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;
    protected static bool $canCreateAnother=false;
    protected function afterSave(): void
{
    $additionalCosts = $this->form->getState()['additionalCosts'] ?? [];

    foreach ($additionalCosts as $item) {
        if (empty($item['additional_cost_id']) && ($item['add_to_standard'] ?? false)) {
            \App\Models\AdditionalCost::create([
                'name' => $item['custom_name'],
                'value' => $item['custom_value'] ?? 0,
                'show_to_customer' => $item['show_to_customer'] ?? true,
                'is_standard' => true,
            ]);
        }
    }
}


}
