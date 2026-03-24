<?php

namespace App\Filament\Resources\CityResource\Pages;

use App\Filament\Resources\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCity extends CreateRecord
{
    protected static string $resource = CityResource::class;
    protected static bool $canCreateAnother = false;
    protected function getRedirectUrl(): string
    {
        return CityResource::getUrl('index');
    }
}
