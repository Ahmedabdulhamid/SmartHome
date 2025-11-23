<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockItemResource\Pages;
use App\Filament\Resources\StockItemResource\RelationManagers;
use App\Models\StockItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Models\StockAdjustmentTransaction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;

use Filament\Tables\Columns\TextColumn;

use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class StockItemResource extends Resource
{
    protected static ?string $model = StockItem::class;


    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'إدارة المخزون';
    protected static ?string $label = 'وحدة مخزون';
    protected static ?string $pluralLabel = 'وحدات المخزون';
    protected static ?string $slug = 'stock-adjustments';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item_name')
                    ->label(__('filament::admin.item_name'))
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('current_quantity')
                    ->label(__('filament::admin.current_quantity'))
                    ->badge()
                    ->color(fn(int $state): string => $state < 10 ? 'danger' : 'success'), // إضافة تنبيه بصري
                TextColumn::make('entity_type')
                    ->label(__('filament::admin.type'))
                    ->formatStateUsing(fn(string $state): string => str_contains($state, 'Variant') ? 'متغير' : 'منتج بسيط'),


            ])
            ->filters([
                //
            ])
          // ... داخل دالة table() في StockItemResource.php

->actions([
    Action::make('adjust_stock') // تغيير الاسم ليعكس التعديل والخصم
        ->label(__('filament::admin.adjust_stock').' 🔄')
        ->icon('heroicon-o-adjustments-horizontal')
        ->color('primary')
        ->form([
            // حقل الرصيد الحالي (مماثل لكودك)
            TextInput::make('current_quantity')
                ->label(__('filament::admin.current_quantity'))
                ->default(fn (StockItem $record) => $record->current_quantity)
                ->disabled()
                ->columnSpanFull(),

            // ⚠️ 1. إضافة حقل اختيار نوع الحركة (Select)
           Select::make('adjustment_type')
                ->label(__('filament::admin.adjustment_type'))
                ->options([
                    'IN' => __('filament::admin.increase_stock_status'),
                    'OUT' =>__('filament::admin.decreament_stock_status'),
                ])
                ->default('IN') // القيمة الافتراضية
                ->required(),

            // حقل الكمية (مماثل لكودك)
            TextInput::make('quantity_to_add')
                ->label(__('filament::admin.quantity'))
                ->numeric()
                ->minValue(1)
                ->required(),

            // حقل السبب (مماثل لكودك)
            Textarea::make('reason')
                ->label(__('filament::admin.reason'))
                ->required()
                ->maxLength(255),
        ])
        ->action(function (array $data, StockItem $record) {
            // ⚠️ 2. تعديل المنطق للتعامل مع IN و OUT
            $type = $data['adjustment_type'];
            $quantity = (int) $data['quantity_to_add'];

            // تحويل الكمية إلى سالبة إذا كان نوع الحركة OUT
            $adjustedQuantity = ($type === 'OUT') ? -$quantity : $quantity;

            DB::beginTransaction();

            try {
                $adjustable = $record->adjustable;

                // 3. تطبيق التعديل على الكمية الحالية
                $newQuantity = $adjustable->quantity + $adjustedQuantity;

                // 🚨 الحماية من المخزون السالب عند النقص (OUT)
                if ($type === 'OUT' && $newQuantity < 0) {
                    DB::rollBack();
                    Notification::make()
                        ->title(__('فشل التعديل'))
                        ->body(__('الكمية المخصومة أكبر من المخزون المتاح ('.$adjustable->quantity.' وحدة).'))
                        ->danger()
                        ->send();
                    return;
                }

                // تحديث وحفظ الكمية الجديدة
                $adjustable->quantity = $newQuantity;
                $adjustable->save();

                // 4. تسجيل الحركة في جدول التدقيق
                StockAdjustmentTransaction::create([
                    'adjustable_id' => $record->entity_id,
                    'adjustable_type' => $record->entity_type,
                    'quantity_changed' => $adjustedQuantity, // تسجيل القيمة المعدلة (سالب أو موجب)
                    'adjustment_type' => $type, // تسجيل النوع المختار
                    'reason' => $data['reason'],
                    'admin_id' => auth()->guard('admin')->id(), // يفضل استخدام auth()->id() إذا كان المستخدمون في نفس جدول المستخدمين
                ]);

                DB::commit();

                Notification::make()
                    ->title(__('تم التعديل بنجاح'))
                    ->body(__("تم تسجيل حركة {$type} بكمية {$quantity} لوحدة: {$record->item_name}"))
                    ->success()
                    ->send();

            } catch (\Throwable $e) {
                DB::rollBack();
                 \Illuminate\Support\Facades\Log::error("Stock Adjustment Failed: " . $e->getMessage() . " - Trace: " . $e->getTraceAsString());
                Notification::make()
                    ->title(__('فشل التعديل'))
                    ->body(__('حدث خطأ أثناء تنفيذ العملية. راجع السجلات.'))
                    ->danger()
                    ->send();
                // Log the error $e
            }
        }),
    // ...
])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
      public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.general_stock_management');
    }
       public static function getPluralLabel(): ?string
    {
        return __('filament::admin.general_stock'); // المسؤولين
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.general_stock'); // مسؤول
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockItems::route('/'),
            'create' => Pages\CreateStockItem::route('/create'),
            'edit' => Pages\EditStockItem::route('/{record}/edit'),
        ];
    }
}
