<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserAdminChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return Cache::remember(
            'filament.admin.dashboard.chart.users.' . now()->format('Y'),
            now()->addMinutes(10),
            function (): array {
                $data = Trend::model(User::class)
                    ->between(
                        start: now()->startOfYear(),
                        end: now()->endOfYear(),
                    )
                    ->perMonth()
                    ->count();

                return [
                    'datasets' => [
                        [
                            'label' => 'Users',
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
        return 'pie';
    }
}
