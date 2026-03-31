<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Admin;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Cache;

class AdminChart extends ChartWidget
{
    protected static ?string $heading = 'Admins';

    protected function getData(): array
    {
        return Cache::remember(
            'filament.admin.dashboard.chart.admins.' . now()->format('Y'),
            now()->addMinutes(10),
            function (): array {
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
