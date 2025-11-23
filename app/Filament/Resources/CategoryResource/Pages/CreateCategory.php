<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
    protected static bool $canCreateAnother=false;
protected static function mutateFormDataBeforeSave(array $data): array
{
    $locales = array_keys(config('translations', ['en']));
    $translations = [];

    foreach ($locales as $locale) {
        if (isset($data[$locale]['name'])) {
            $translations[$locale] = [
                'name' => $data[$locale]['name'],
            ];
        }
        unset($data[$locale]); // إزالة الحقول غير الموجودة في جدول categories
    }

    $data['translations'] = $translations;

    return $data;
}


}
