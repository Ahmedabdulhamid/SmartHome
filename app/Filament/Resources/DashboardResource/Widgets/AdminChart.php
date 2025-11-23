<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Admin;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
class AdminChart extends ChartWidget
{

    protected static ?string $heading = 'Admins';

    protected function getData(): array
    {
         $data = Trend::model(Admin::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();
         return [
        'datasets' => [
            [
                'label' => 'Admins',
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
