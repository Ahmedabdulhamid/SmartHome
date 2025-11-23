<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuotationResource\Pages;
use App\Filament\Resources\QuotationResource\RelationManagers;
use App\Mail\ReserveQuotationMail;
use App\Models\Quotation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Template;
use App\Models\ProductVariant;
use App\Models\Rfq;
use Filament\Forms\Components\Repeater;


use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\TextColumn;


use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class QuotationResource extends Resource
{
    protected static ?string $model = Quotation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 🔹 العنوان متعدد اللغات
                Tabs::make('Languages')
                    ->tabs([
                        Tab::make('Arabic')->schema([
                            TextInput::make('title.ar')
                                ->label(__('filament::admin.title_ar'))
                                ->required()
                                ->columnSpanFull(),
                        ]),
                        Tab::make('English')->schema([
                            TextInput::make('title.en')
                                ->label(__('filament::admin.title_en'))
                                ->required()
                                ->columnSpanFull(),
                        ]),
                    ])
                    ->columnSpanFull(),

                // 🔹 بيانات أساسية
                Section::make(__('filament::admin.basic_info'))
                    ->schema([


                        DatePicker::make('date')
                            ->label(__('filament::admin.date'))
                            ->default(now())
                            ->required()
                            ->native(false)
                            ->rules(['after_or_equal:today']),

                        Select::make('currency_id')
                            ->relationship('currency', 'code')
                            ->label(__('filament::admin.currency'))
                            ->searchable()
                            ->reactive()
                            ->preload()
                            ->required()
                            ->options(function (Get $get) {
                                return \App\Models\Currency::whereHas('rfqs', function ($query) {
                                    $query->where('status', 'pending');
                                })->pluck('code', 'id');
                            }),

                        // 🔹 RFQ Selector
                        Select::make('rfq_id')
                            ->label(__('filament::admin.select_rfq'))
                            ->options(function (Get $get) {
                                $currencyId = $get('currency_id');
                                if (!$currencyId) return [];
                                return Rfq::where('status', 'pending')
                                    ->where('currency_id', $currencyId)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->getOptionLabelUsing(function (?int $value): ?string {
                                // هذا السطر يضمن عرض الاسم (name) بدلاً من الـ ID في وضع التعديل
                                // إذا كان الـ ID موجودًا في قاعدة البيانات.
                                return $value ? \App\Models\Rfq::find($value)?->name : null;
                            })
                            ->reactive()
                            ->searchable()
                            ->nullable()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $rfq = Rfq::find($state);
                                    $rfqItems = $rfq->items()->with(['product', 'variant'])->get();

                                    if ($rfqItems->count()) {
                                        $itemsData = $rfqItems->map(function ($item) {
                                            $product = $item->product;

                                            if ($product->has_variants) {
                                                $basePrice = $item->variant
                                                    ? ($product->has_discount
                                                        ? $item->variant->price * (1 - $product->discount_percentage / 100)
                                                        : $item->variant->price)
                                                    : ($product->has_discount
                                                        ? $product->base_price * (1 - $product->discount_percentage / 100)
                                                        : $product->base_price ?? 0);
                                            } else {
                                                $basePrice = $product->base_price ?? 0;
                                                if ($product->has_discount) {
                                                    $basePrice *= (1 - $product->discount_percentage / 100);
                                                }
                                            }

                                            return [
                                                'rfq_item_id' => $item->id,
                                                'product_id' => $item->product_id,
                                                'product_variant_id' => $item->product_variant_id,
                                                'quantity' => $item->quantity ?? 1,
                                                'base_price' => $basePrice,
                                            ];
                                        })->toArray();

                                        $set('items', $itemsData);
                                        $set('manual_base_price', null);
                                        $set('manual_margin_percentage', null);
                                        $set('manual_total', null);
                                    } else {
                                        // RFQ موجود لكن مفيهوش items
                                        $set('items', []);
                                        $set('manual_base_price', $rfq->expected_price ?? 0);
                                    }
                                } else {
                                    // مفيش RFQ مختار
                                    $set('items', []);
                                    $set('manual_base_price', null);
                                }
                            }),

                        // 🔹 Manual fields لو ما فيش items
                        TextInput::make('manual_base_price')
                            ->label(__('filament::admin.base_price'))
                            ->numeric()
                            ->reactive()
                            ->visible(fn(Get $get) => empty($get('items'))),
                        Select::make('tax_id')
                            ->label(__('filament::admin.tax'))
                            ->relationship('tax', 'name')
                            ->reactive()
                            ->visible(fn(Get $get) => empty($get('items'))),

                        TextInput::make('manual_margin_percentage')
                            ->label(__('filament::admin.margin_precentage'))
                            ->numeric()
                            ->suffix('%')
                            ->default(1)
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $base = $get('manual_base_price') ?? 0;
                                $set('manual_total', $base + ($base * ($state ?? 0) / 100));
                            })
                            ->visible(fn(Get $get) => empty($get('items'))),

                        TextInput::make('manual_total')
                            ->label(__('filament::admin.selling_price'))
                            ->numeric()
                            ->disabled()
                            ->visible(fn(Get $get) => empty($get('items'))),

                        TextInput::make('delivery_place')->label(__('filament::admin.delivery_place')),

                        DatePicker::make('delivery_time')->label(__('filament::admin.delivery_time'))->native(false)->rules(['after_or_equal:today']),



                    ])->columns(2),


                Repeater::make('items')->relationship('items')
                    ->label(__('filament::admin.items'))
                    ->defaultItems(1)->schema([
                        Hidden::make('rfq_item_id'),
                        Select::make('product_id')->label(__('filament::admin.product'))
                            ->options(function (Get $get) {
                                $rfqId = $get('../../rfq_id');
                                $currencyId = $get('../../currency_id');
                                if (!$rfqId) {
                                    return $currencyId ? \App\Models\Product::where('currency_id', $currencyId)->pluck('name', 'id')->toArray() : [];
                                }
                                $productIds = \App\Models\RfqItem::where('rfq_id', $rfqId)->pluck('product_id')->filter()->unique()->toArray();
                                return \App\Models\Product::whereIn('id', $productIds)->pluck('name', 'id')->toArray();
                            })->reactive()->required()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $set('product_variant_id', null);
                                $rfqItemId = $get('rfq_item_id');
                                if ($rfqItemId && ($rfqItem = \App\Models\RfqItem::find($rfqItemId)) && $rfqItem->product_id == $state) {
                                    $set('base_price', $rfqItem->expected_price ?? 0);
                                    return;
                                }
                                $product = \App\Models\Product::find($state);
                                $set('base_price', $product->price ?? 0);
                            })->columnSpan(1),
                        Select::make('product_variant_id')->label(__('filament::admin.variant'))->options(function (Get $get) {
                            $productId = $get('product_id');
                            if (!$productId) return [];
                            $rfqId = $get('../../rfq_id');
                            if (!$rfqId) {
                                return \App\Models\ProductVariant::where('product_id', $productId)->pluck('name', 'id')->toArray();
                            }
                            $variantIds = \App\Models\RfqItem::where('rfq_id', $rfqId)->where('product_id', $productId)->pluck('product_variant_id')->filter()->unique()->toArray();
                            return \App\Models\ProductVariant::whereIn('id', $variantIds)->where('product_id', $productId)->pluck('name', 'id')->toArray();
                        })->nullable()->reactive()->afterStateUpdated(function ($state, Set $set, Get $get) {
                            $rfqItemId = $get('rfq_item_id');
                            $productId = $get('product_id');
                            $variant = \App\Models\ProductVariant::find($state);
                            $product = \App\Models\Product::find($productId);
                            if ($product->has_variants) {
                                if ($product->has_discount) {
                                    $set('base_price', $variant ? $variant->price * (1 - $product->discount_percentage / 100) : $product->base_price * (1 - $product->discount_percentage / 100));
                                } else {
                                    $set('base_price', $variant ? $variant->price : $product->base_price ?? 0);
                                }
                            } else {
                                if ($product->has_discount) {
                                    $set('base_price', $product->base_price * (1 - $product->discount_percentage / 100));
                                } else {
                                    $set('base_price', $product->base_price ?? 0);
                                }
                            }
                        })->columnSpan(1),
                        TextInput::make('quantity')->label(__('filament::admin.quantity'))->numeric()->default(1)->required()->disabled()->dehydrated(true)->columnSpan(1),
                        TextInput::make('base_price')->label(__('filament::admin.base_price'))->numeric()->disabled()->reactive()->afterStateHydrated(function ($state, Set $set, Get $get, $record) {
                            if ($state === null) {
                                $set('base_price', 0);
                            }
                        })->columnSpan(1),
                        Select::make('tax_id')->label(__('filament::admin.tax'))->options(\App\Models\Tax::pluck('name', 'id'))->nullable()->columnSpan(1),
                        TextInput::make('margin_percentage')->label(__('filament::admin.margin_percentage'))->numeric()->suffix('%')->required()->default(0)->reactive()->afterStateUpdated(function ($state, Set $set, Get $get) {
                            $basePrice = $get('base_price') ?? 0;
                            $set('selling_price', $basePrice + ($basePrice * $state / 100));
                        })->columnSpan(1),
                        TextInput::make('selling_price')->label(__('filament::admin.selling_price'))->numeric()->disabled()->reactive()->afterStateHydrated(function ($state, Set $set, Get $get) {
                            $basePrice = $get('base_price') ?? 0;
                            $margin = $get('margin_percentage') ?? 0;
                            $set('selling_price', $basePrice + ($basePrice * $margin / 100));
                        })->columnSpan(1),

                    ])->columns(3)->columnSpanFull()->hidden(fn(Get $get) => !$get('rfq_id'))
                    ->hidden(function (Get $get) {
                        $rfqId = $get('rfq_id');
                        if (!$rfqId) return true;

                        $rfq = \App\Models\Rfq::withCount('items')->find($rfqId);

                        // لو RFQ ما فيهش items → Repeater مخفي
                        return $rfq && $rfq->items_count == 0;
                    })->addable(false)
                    ->deletable(false)     // <--- لإخفاء زر "إضافة عنصر"
                ,


                Repeater::make('additionalCosts')
                    ->relationship('additionalCosts')
                    ->schema([
                        Select::make('additional_cost_id')
                            ->label(__('filament::admin.main_costs'))
                            ->options(\App\Models\AdditionalCost::pluck('name', 'id')->toArray())
                            ->nullable(),
                        Section::make(__('filament::admin.sub_costs'))
                            ->schema([
                                TextInput::make('custom_name')
                                    ->label(__('filament::admin.custom_name')),
                                TextInput::make('custom_value')
                                    ->label(__('filament::admin.custom_value'))
                                    ->numeric(),
                            ]),
                        Toggle::make('save_as_main')
                            ->label(__('filament::admin.save_as_main')),
                        Toggle::make('show_to_customer')
                            ->label(__('filament::admin.show_to_customer'))
                            ->default(true),
                    ])
                    ->afterStateUpdated(function ($state) {
                        Log::info('📌 حالة Repeater اتغيرت', ['state' => $state]);
                    })
                    ->columnSpanFull()
                // 🔑 الإخفاء الشرطي
            ]);
    }





    public static function table(Table $table): Table
    {
        return $table
            ->columns([


                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament::admin.title'))
                    ->getStateUsing(fn($record) => $record->getTranslation('title', app()->getLocale()))
                    ->sortable()
                    ->limit(50),

                TextColumn::make('is_reserved')
                    ->label(__('filament::admin.reserve_status'))
                    ->formatStateUsing(fn(bool $state) => $state ? __('filament::admin.reserved') : __('filament::admin.on_hold')) // عرض نصي
                    ->badge() // لعرضها كشارة ملونة
                    ->color(fn(bool $state): string => $state ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('hiddenCosts.description')
                    ->label(__('filament::admin.vat_description'))
                    ->toggleable(isToggledHiddenByDefault: true),



                TextColumn::make('total')
                    ->label(__('filament::admin.total'))
                    ->money(
                        // نستخدم here دالة get state للحصول على قيمة العملة من الـ record
                        fn($record) => $record->currency->code ?? 'USD'
                    ),


                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament::admin.created_at'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('view')
                    ->label(__('filament::admin.view'))

                    ->url(fn(Quotation $record) => self::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye')
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('Reserve')
                    ->label(__('filament::admin.reserve'))
                    ->color('success')
                    ->visible(
                        fn($record): bool =>
                        !$record->is_reserved &&
                            auth()->guard('admin')->user()->hasRole('sales manager')
                    )
                    ->action(function ($record) {


                        if ($record->is_reserved) {
                            Notification::make()
                                ->title(__('filament::admin.operation_error'))
                                ->body(__('filament::admin.operation_error_message'))
                                ->danger()
                                ->send();
                            return;
                        }

                        // ⚠️ الخطوة 2: استخدام DB::transaction لضمان الأمان
                        DB::transaction(function () use ($record) {

                            // 1. تحديث حالة الـ Quotation
                            $record->update([

                                'is_reserved' => 1,

                            ]);


                            if ($record->items->isNotEmpty()) {

                                foreach ($record->items as $item) {

                                    $quantityToReserve = $item->quantity;
                                    $product = $item->product;
                                    $variant = $item->variant;
                                    $stockEntity = $variant ?? $product;

                                    // تنفيذ منطق المخزون فقط إذا كان العنصر مرتبطاً بكيان مخزون
                                    if ($stockEntity && $item->product_id) {

                                        // أ. تحديث المخزون باستخدام DB::raw (البديل الآمن لـ decrement/increment)
                                        $stockEntity->update([
                                            // تقليل الكمية المتاحة للبيع
                                            'quantity'       => DB::raw("quantity - {$quantityToReserve}"),
                                            // زيادة الكمية المحجوزة
                                            'reserved_stock' => DB::raw("reserved_stock + {$quantityToReserve}"),
                                        ]);

                                        // ب. تسجيل حركة المخزون (Inventory Transaction)
                                        $item->inventoryTransactions()->create([
                                            'quotation_id'       => $record->id,
                                            'quotation_item_id'  => $item->id,
                                            'product_id'         => $product->id,
                                            'product_variant_id' => $variant ? $variant->id : null,
                                            'quantity'           => -$quantityToReserve, // تسجيل الكمية بالسالب
                                            'type'               => 'reservation',
                                            'reference'          => 'Reservation for Quotation #' . $record->id,
                                        ]);
                                    }
                                    // حالة الـ Lump Sum/الخدمات: يتم تخطي هذا المنطق هنا
                                }
                            }
                        });

                        // 3. إشعار النجاح
                        Notification::make()
                            ->title('تم تأكيد الحجز بنجاح')
                            ->body('تم تأكيد عرض السعر رقم ' . $record->id . ' وحجز الكميات المطلوبة.')
                            ->success()
                            ->send();
                            Mail::to($record->rfq->email)->send(new ReserveQuotationMail($record));
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
        return __('filament::admin.quotation_management');
    }

    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.quotations'); // عروض الأسعار
    }
    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        // التحقق مما إذا كان حقل rfq_id تم ملؤه في النموذج
        if (isset($data['rfq_id']) && $data['rfq_id']) {
            $rfqId = $data['rfq_id'];

            // 1. جلب سجل RFQ وتحديث حالته
            $rfq = Rfq::find($rfqId);

            if ($rfq) {
                // 2. تعيين الحالة الجديدة
                $rfq->status = 'replied';
                $rfq->save();
            }

            // 3. (اختياري) يمكنك حذف 'rfq_item_id' من الـ items إذا لم يكن لديك عمود له في QuotationItem
            // لكن بما أنك تستخدمه للربط في الـ Repeater، سنتركه ليتم حفظه.
        }

        // إرجاع البيانات المحدثة ليتمكن Filament من إنشاء سجل Quotation
        return $data;
    }
    public static function getLabel(): ?string
    {
        return __('filament::admin.quotation'); // عرض سعر
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['items.product', 'items.variant', 'currency']); // eager load
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotations::route('/'),
            'create' => Pages\CreateQuotation::route('/create'),
            'edit' => Pages\EditQuotation::route('/{record}/edit'),
            'view' => Pages\ViewQuotation::route('/{record}'),
        ];
    }
}
