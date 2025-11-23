<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryTransactionResource\Pages;
use App\Filament\Resources\InventoryTransactionResource\RelationManagers;
use App\Models\InventoryTransaction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class InventoryTransactionResource extends Resource
{
    protected static ?string $model = InventoryTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                // 1. تحديد الحركة (What Happened)
                TextColumn::make('type')
                    ->label(__('filament::admin.type'))
                    ->can(fn ($record) => auth()->guard('admin')->user()->hasRole('store') || auth()->guard('admin')->user()->hasRole('Super Admin'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'in'          => 'success', // إدخال مخزون
                        'out'         => 'danger',  // إخراج/بيع مباشر
                        'reservation' => 'warning', // حجز طلب
                        'cancellation' => 'info',
                        'reservation_canceled' => 'secondary',     // إلغاء حجز
                    })->formatStateUsing(fn(string $state): string => match ($state) {
                        'in' => __('filament::admin.in'),
                        'out' => __('filament::admin.out'),
                        'reservation' => __('filament::admin.reservation'),
                        'cancellation' => __('filament::admin.cancellation'),
                        'reservation_canceled' => __('filament::admin.reservation_canceld'),
                        default => ucfirst($state),
                    })
                    ->sortable(),

                // 2. الكمية (How Much)
                TextColumn::make('quantity')
                    ->label(__('filament::admin.quantity_affected'))
                    ->numeric()
                    ->sortable(),

                // 3. المنتج (The Item)
                TextColumn::make('product.name')
                    ->label(__('filament::admin.product'))
                    ->searchable()->limit(40)
                    ->placeholder('N/A'), // في حال لم يتم تعيين المنتج (نظريًا لا يجب أن يحدث في هذا الجدول)

                // 4. الوحدة الفرعية (Variant)
                TextColumn::make('variant.name') // افترض أن الـ variant model لديه حقل 'name'
                    ->label(__('filament::admin.variant'))
                    ->placeholder('-')->limit(40),

                // 5. مصدر الحركة (Why)
                TextColumn::make('quotation_id')
                    ->label(__('filament::admin.quoatation'))
                    ->url(fn($record) => $record->quotation ? \App\Filament\Resources\QuotationResource::getUrl('view', ['record' => $record->quotation]) : null)
                    ->formatStateUsing(fn($state) => "عرض رقم #{$state}")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // يمكن إخفاؤه افتراضيًا

                // 6. المرجع والوصف
                TextColumn::make('reference')
                    ->label(__('filament::admin.reference'))
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                // 7. التوقيت (When)
                TextColumn::make('created_at')
                    ->label(__('filament::admin.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([

                // 1. فلتر حسب نوع الحركة (الأكثر أهمية)
                SelectFilter::make('type')
                    ->label(__('filament::admin.type'))
                    ->options([
                        'in' => 'إدخال / جرد',
                        'out' => 'بيع نهائي',
                        'reservation' => 'حجز طلب',
                        'cancellation' => 'إلغاء حجز',
                    ])
                    ->multiple(), // يسمح باختيار أكثر من نوع في نفس الوقت

                // 2. فلتر حسب المنتج (لمراجعة تاريخ منتج معين)
                SelectFilter::make('product_id')
                    ->label(__('filament::admin.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),

                // 3. فلتر حسب عرض السعر/الطلب (لتتبع كل حركات طلب واحد)
                SelectFilter::make('quotation_id')
                    ->label(__('filament::admin.quotation'))
                    // نفترض أن موديل Quotation لديه حقل مرجعي أو رقم
                    ->relationship('quotation', 'id')
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn($record) => "طلب رقم #{$record->id}"),

                // 4. فلتر حسب التاريخ (لمراجعة الحركة خلال فترة زمنية)
                Filter::make('created_at')
                    ->label(__('filament::admin.created_at'))
                    ->form([
                        DatePicker::make('from')
                            ->label('من تاريخ'),
                        DatePicker::make('until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                // داخل InventoryTransactionResource ->table() ->actions()

                // مثال لـ Action يسمح بتعديل نوع الحركة
                Tables\Actions\Action::make('change_transaction_type')
                    ->label('تغيير نوع الحركة')
                    ->visible(fn(\App\Models\InventoryTransaction $record) => $record->type === 'reservation') // يظهر فقط للحجوزات
                    ->form([
                        Forms\Components\Select::make('new_type')
                            ->label('تغيير نوع الحركة إلى')
                            ->options([
                                'out'          => 'بيع نهائي (تم الشحن)',
                                'cancellation' => 'إلغاء الحجز',
                            ])
                            ->required(),
                    ])
                    ->action(function (\App\Models\InventoryTransaction $record, array $data) {

                        // ⚠️ الخطوة 1: استخدام DB::transaction لضمان الأمان
                        DB::transaction(function () use ($record, $data) {

                            // تحديد الكيان المسؤول عن المخزون وكمية الحركة الأصلية
                            // (استخدام العلاقات مباشرة بدلاً من الميثودات)
                            $stockEntity = $record->product_variant_id ? $record->variant : $record->product;

                            // الكمية المحجوزة الأصلية (يجب أن تكون سالبة، نأخذ القيمة المطلقة)
                            $reservedQuantity = abs($record->quantity);

                            // ⛔️ تم إزالة السطر $record->update(['type' => $data['new_type']])

                            // 1. منطق الإلغاء (Cancellation Logic)
                            if ($data['new_type'] === 'cancellation') {

                                if ($stockEntity) {
                                    // أ. تحديث المخزون (إعادة الكمية المحجوزة للمخزون المتاح)
                                    $stockEntity->update([
                                        'quantity'       => DB::raw("quantity + {$reservedQuantity}"),
                                        'reserved_stock' => DB::raw("reserved_stock - {$reservedQuantity}"),
                                    ]);

                                    // ب. إنشاء سجل حركة جديد لعملية الإلغاء (السبب: السجل التاريخي)
                                    InventoryTransaction::create([
                                        'quotation_id'       => $record->quotation_id,
                                        'quotation_item_id'  => $record->quotation_item_id,
                                        'product_id'         => $record->product_id,
                                        'product_variant_id' => $record->product_variant_id,
                                        'quantity'           => $reservedQuantity, // تسجيلها كقيمة موجبة
                                        'type'               => 'cancellation',
                                        'reference'          => 'Cancellation of Transaction #' . $record->id . ' (Original Type: ' . $record->type . ')',
                                    ]);

                                    // ج. تحديث السجل الأصلي لتوثيق أنه تم إلغاءه (اختياري، يفضل إضافة enum جديد)
                                    // إذا لم تضف 'reservation_canceled'، يمكنك تركه 'reservation' أو استبداله بنوع جديد.
                                    $record->update(['type' => 'reservation_canceled']);
                                }
                            }

                            // 2. منطق البيع النهائي (Sale Logic)
                            else if ($data['new_type'] === 'out') {

                                // أ. تحديث نوع السجل الأصلي إلى 'out' (البيع النهائي)
                                $record->update(['type' => 'out']);

                                // ب. تحديث المخزون (خصم الكمية من المحجوز فقط)
                                if ($stockEntity) {
                                    $stockEntity->update([
                                        // تقليل المخزون المحجوز (المخزون المتاح تم خصمه بالفعل في خطوة Reserve)
                                        'reserved_stock' => DB::raw("reserved_stock - {$reservedQuantity}"),
                                    ]);
                                }
                            }

                            Notification::make()->title('تم تعديل نوع الحركة بنجاح.')->success()->send();
                        });
                    })
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
        return __('filament::admin.inventory_transactions_management');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.inventory_transactions'); // المسؤولين
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.inventory_transactions'); // مسؤول
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryTransactions::route('/'),
            'create' => Pages\CreateInventoryTransaction::route('/create'),
            'edit' => Pages\EditInventoryTransaction::route('/{record}/edit'),
        ];
    }
}
