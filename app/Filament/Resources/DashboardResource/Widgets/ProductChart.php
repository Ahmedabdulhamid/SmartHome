<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Cache;

class ProductChart extends ChartWidget
{
    protected static ?string $heading = 'Products';

    protected function getData(): array
    {
        return Cache::remember(
            'filament.admin.dashboard.chart.products.' . now()->format('Y'),
            now()->addMinutes(10),
            function (): array {
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
                            'color' => 'danger',
                            'data' => $data->map(fn (TrendValue $value) => $value->aggregate)->all(),
                        ],
                    ],
                    'labels' => $data->map(fn (TrendValue $value) => $value->date)->all(),
                ];
            },
        );
    }

    protected function getType(): string
    {
        return 'line';
    }
}
