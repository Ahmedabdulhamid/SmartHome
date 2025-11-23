<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\Admin;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(__('filament::admin.admins'), Admin::query()->count())
                ->description(__('filament::admin.admins_description'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make(__('filament::admin.products'), Product::query()->count())
                ->description(__('filament::admin.products_description'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info')
                ->chart([3, 5, 8, 2, 10, 6, 12]),

            Stat::make(__('filament::admin.users'), User::query()->count())
                ->description(__('filament::admin.users_description'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning')
                ->chart([5, 3, 9, 6, 11, 7, 14]),
        ];
    }
}
