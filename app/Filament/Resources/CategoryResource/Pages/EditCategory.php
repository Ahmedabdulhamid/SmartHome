<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
protected function mutateFormDataBeforeSave(array $data): array
{
    $locales = array_keys(config('translations', ['en']));
    $translations = [];

    foreach ($locales as $locale) {
        if (isset($data[$locale]['name'])) {
            $translations[$locale] = [
                'name' => $data[$locale]['name'],
            ];
        }
        unset($data[$locale]);
    }

    $data['translations'] = $translations;

    return $data;
}}



