<?php

namespace App\Filament\Resources\ShippingPriceResource\Pages;

use App\Filament\Resources\ShippingPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShippingPrices extends ListRecords
{
    protected static string $resource = ShippingPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
