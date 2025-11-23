<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
    protected static bool $canCreateAnother=false;
      protected function mutateFormDataBeforeCreate(array $data): array
    {
        // تسجيل حالة الفورم قبل الحفظ
        Log::error('Wizard Step Data Before Create', [
            'data' => $data,
        ]);

        return $data;
    }
protected function afterCreate(): void
{
    $record = $this->record;

    // جميع steps
    $state = $this->form->getState(true);

    if (!empty($state['attributes'])) {
        $attributeIds = collect($state['attributes'])
            ->pluck('attribute_id')
            ->filter()
            ->toArray();

        $record->attributes()->sync($attributeIds);
    }

    Log::info('Product created with attributes: ', $state['attributes'] ?? []);
}


}
