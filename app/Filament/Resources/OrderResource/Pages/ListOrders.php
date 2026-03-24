<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    protected function getListeners(): array
    {
        // الحصول على Admin ID
        // ملاحظة: Livewire Component يعمل بـ auth() بشكل طبيعي
        $adminId = auth()->guard('admin')->user()->id;

        // الصيغة الصحيحة لتضمين المتغير في مفتاح المصفوفة باستخدام Double Quotes
        return [
            "echo:admin.orders.{$adminId},order.created" => 'refreshList',
            // أو دمج السلاسل:
            // 'echo:admin.orders.' . $adminId . ',order.created' => 'refreshList',
        ];
    }

    public function refreshList()
    {
        // إضافة رسالة Log هنا للتأكد من استجابة المكون للحدث
        Log::info('Filament List Refresh Triggered.', [
            'user_id' => auth()->user()->id,
            'channel' => 'admin.orders.' . auth()->user()->id,
            'event' => 'order.created'
        ]);

        // الأمر الفعلي لإعادة تحميل بيانات الجدول
        $this->emit('$refresh');


        $this->notify('success', 'تم تحديث قائمة الطلبات.', shouldClose: true);
    }


    public function getTabs(): array
    {
        return [

            // All Orders
            'all' => Tab::make(__('filament::admin.all_orders'))
                ->badge(Order::count())
                ->badgeColor('primary'),

            // Today's Orders
            'today' => Tab::make(__('filament::admin.today'))
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->whereDate('created_at', today())
                )
                ->badge(
                    Order::whereDate('created_at', today())->count()
                )
                ->badgeColor('success'),

            // This Week Orders
            'this_week' => Tab::make(__('filament::admin.this_week'))
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek(),
                    ])
                )
                ->badge(
                    Order::whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek(),
                    ])->count()
                )
                ->badgeColor('info'),

            // This Month Orders
            'this_month' => Tab::make(__('filament::admin.this_month'))
                ->modifyQueryUsing(
                    fn(Builder $query) => $query->whereYear('created_at', now()->year)
                        ->whereMonth('created_at', now()->month)
                )
                ->badge(
                    Order::whereYear('created_at', now()->year)
                        ->whereMonth('created_at', now()->month)
                        ->count()
                )
                ->badgeColor('warning'),
        ];
    }
}
