<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
class ProductChart extends ChartWidget
{
    protected static ?string $heading = 'Products';

     protected function getData(): array
    {
         $data = Trend::model(Product::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();
         return [
        'datasets' => [
            [
                'label' => 'Products',
                'color'=>"danger",
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
    }


    protected function getType(): string
    {
        return 'line';
    }
}
