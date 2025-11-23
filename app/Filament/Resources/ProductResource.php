<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Image;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use App\Models\ProductVariant;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;
use stdClass;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Filters\Filter;
use Illuminate\Validation\Rule;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Wizard::make([
                        Step::make(__('filament::admin.general'))->schema([
                            TextInput::make('name.en')
                                ->label(__('filament::admin.name_english'))

                                ->required(),

                            TextInput::make('name.ar')
                                ->label(__('filament::admin.name_arabic'))
                                ->required(),

                            Forms\Components\RichEditor::make('description.en')
                                ->label(__('filament::admin.desc_en'))
                                ->required(),

                            Forms\Components\RichEditor::make('description.ar')
                                ->label(__('filament::admin.desc_ar'))
                                ->required(),

                            Select::make('category_id')
                                ->label(__('filament::admin.category'))
                                ->relationship('category', 'name', fn($query) => $query->where('type', 'products'))
                                ->getOptionLabelFromRecordUsing(fn($record) => $record->getTranslation('name', app()->getLocale()))
                                ->searchable()
                                ->preload()
                                ->required(),


                            Select::make('brand_id')
                                ->label(__('filament::admin.brand'))
                                ->relationship('brand', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Forms\Components\Select::make('currency_id')
                                ->label(__('filament::admin.currency'))
                                ->relationship('currency', 'code') // هنا بتحدد العمود اللي يتعرض
                                ->searchable()
                                ->preload()
                                ->required(),

                            Toggle::make('has_variants')
                                ->label(__('filament::admin.has_var'))
                                ->reactive(),

                            TextInput::make('base_price')
                                ->label(__('filament::admin.base_price'))
                                ->numeric()
                                ->visible(fn($get) => !$get('has_variants')),
                            Tabs::make(__('filament::admin.translation'))
                                ->tabs([
                                    Tab::make(__('filament::admin.highlights_en'))
                                        ->schema([
                                            RichEditor::make('highlights.en')
                                                ->label(__('filament::admin.highlights_en')),

                                        ]),

                                    Tab::make(__('filament::admin.highlights_ar'))
                                        ->schema([
                                            RichEditor::make('highlights.ar')
                                                ->label(__('filament::admin.highlights_ar')),

                                        ]),

                                ])->visible(fn($get) => !$get('has_variants')),
                            Tabs::make(__('filament::admin.translation'))
                                ->tabs([
                                    Tab::make(__('filament::admin.english'))
                                        ->label(__('filament::admin.drawbacks_en'))
                                        ->schema([
                                            RichEditor::make('drawbacks.en')
                                                ->label(__('filament::admin.drawbacks_en')),

                                        ]),

                                    Tab::make(__('filament::admin.arabic'))
                                        ->label(__('filament::admin.drawbacks_ar'))
                                        ->schema([
                                            RichEditor::make('drawbacks.ar')
                                                ->label(__('filament::admin.drawbacks_ar')),

                                        ]),

                                ])->visible(fn($get) => !$get('has_variants')),


                            Toggle::make('manage_quantity')
                                ->label(__('filament::admin.manage_quantity'))
                                ->visible(fn($get) => !$get('has_variants')),

                            TextInput::make('quantity')
                                ->label(__('filament::admin.quantity'))
                                ->numeric()
                                ->visible(fn($get) => !$get('has_variants')),

                            // ✅ الحقول الجديدة
                            Toggle::make('has_discount')
                                ->label(__('filament::admin.has_discount'))
                                ->reactive(),

                            Forms\Components\TextInput::make('discount_percentage')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->step(0.01)
                                ->suffix('%')
                                ->label(__('filament::admin.discount_precentage'))
                                ->visible(fn($get) => $get('has_discount')),


                            DatePicker::make('start_at')
                                ->label(__('filament::admin.start_at'))
                                ->visible(fn($get) => $get('has_discount'))
                                ->rules(['after_or_equal:today'])
                                ->native(false),

                            // يمكنك الإبقاء عليها أو حذفها إذا لم تستخدم أي شيء آخر منها

                            // ... داخل دالة form() ...

                            DatePicker::make('ends_at')
                                ->label(__('filament::admin.ends_at'))
                                ->rules(function ($get) { // استخدام Closure لتحديد القواعد بشكل ديناميكي
                                    // جلب قيمة start_at
                                    $startDate = $get('start_at');

                                    // إذا كان هناك تاريخ بداية، أضف قاعدة after_or_equal:
                                    $rules = [
                                        'after_or_equal:today', // القاعدة الأولى: لا يمكن أن يكون في الماضي
                                    ];

                                    if ($startDate) {
                                        // ✅ الحل: نستخدم قاعدة 'after_or_equal' كسلسلة نصية
                                        // ونمرر لها قيمة حقل start_at
                                        $rules[] = 'after_or_equal:' . $startDate;
                                    }

                                    return $rules;
                                })
                                ->visible(fn($get) => $get('has_discount'))
                                ->native(false),

                            Select::make('status')
                                ->label(__('filament::admin.status'))
                                ->options([
                                    'active' => __('filament::admin.active'),
                                    'inactive' => __('filament::admin.inactive'),
                                ])
                                ->default('active')
                                ->required(),
                        ]),


                        Step::make(__("filament::admin.variants"))->schema([
                            Repeater::make('variants')
                                ->relationship('variants')
                                ->visible(fn($get) => $get('has_variants'))
                                ->schema([

                                    TextInput::make('name.en')
                                        ->label(__('filament::admin.variants_name_en'))
                                        ->required(),

                                    TextInput::make('name.ar')
                                        ->label(__('filament::admin.variants_name_ar'))
                                        ->required(),


                                    Tabs::make(__('filament::admin.translation'))
                                        ->tabs([
                                            Tab::make(__('filament::admin.highlights_en'))

                                                ->schema([
                                                    RichEditor::make('highlights.en'),

                                                ]),

                                            Tab::make(__('filament::admin.highlights_ar'))

                                                ->schema([
                                                    RichEditor::make('highlights.ar'),

                                                ]),

                                        ]),
                                    Tabs::make(__('filament::admin.translation'))
                                        ->tabs([
                                            Tab::make(__('filament::admin.drawbacks_en'))

                                                ->schema([
                                                    RichEditor::make('drawbacks.en'),

                                                ]),

                                            Tab::make(__('filament::admin.drawbacks_ar'))

                                                ->schema([
                                                    RichEditor::make('drawbacks.ar'),

                                                ]),

                                        ]),





                                    TextInput::make('price')
                                        ->label(__('filament::admin.price'))
                                        ->numeric()
                                        ->required(),

                                    TextInput::make('quantity')
                                        ->label(__('filament::admin.quantity'))
                                        ->numeric()
                                        ->default(0),

                                    Toggle::make('manage_quantity')
                                        ->label(__('filament::admin.manage_quantity'))
                                        ->default(true),

                                    // صور الـ Variant
                                    FileUpload::make('variantImages')
                                        ->label(__('filament::admin.product_images'))
                                        ->multiple()
                                        ->disk('public')
                                        ->directory('variants')
                                        ->image()
                                        ->preserveFilenames()
                                        ->required()
                                        ->afterStateHydrated(function ($set, $record) {
                                            if ($record) {
                                                $set('variantImages', $record->variantImages->pluck('path')->toArray());
                                            }
                                        })
                                        ->saveUploadedFileUsing(function ($file) {
                                            return $file->storeAs(
                                                'variants',
                                                'variant_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension(),
                                                'public'
                                            );
                                        })
                                        ->saveRelationshipsUsing(function ($state, $record) {
                                            $record->variantImages()->delete();

                                            foreach ($state as $path) {
                                                $record->variantImages()->create([
                                                    'path' => $path,
                                                ]);
                                            }
                                        }),

                                    // Attribute Values
                                    Repeater::make('pivot_attribute_values')
                                        ->label(__('filament::admin.attr_values'))
                                        ->relationship('attributeValuesPivot')
                                        ->schema([
                                            Select::make('attribute_id')
                                                ->label(__('filament::admin.attribute'))
                                                ->options(function () {
                                                    // ✅ تحسين: استخدام query builder للحصول على options
                                                    return Attribute::query()
                                                        ->get()
                                                        ->mapWithKeys(fn($attr) => [$attr->id => $attr->getTranslation('name', app()->getLocale()) ?? 'No Name']);
                                                })
                                                ->reactive()
                                                ->required()
                                                ->afterStateUpdated(fn($state, $set) => $set('attribute_value_id', null)),

                                            Select::make('attribute_value_id')
                                                ->label(__('filament::admin.value'))
                                                ->options(function ($get) {
                                                    $attrId = $get('attribute_id');
                                                    if (!$attrId) return [];
                                                    // ✅ تحسين: استخدام where لتصفية القيم
                                                    return AttributeValue::where('attribute_id', $attrId)
                                                        ->get()
                                                        ->mapWithKeys(fn($v) => [$v->id => $v->value ?? 'No Value']);
                                                })
                                                ->required(),
                                        ])
                                        ->columns(1)
                                        ->createItemButtonLabel(__('filament::admin.add_attr_value'))
                                        ->columnSpan('full'),

                                ])
                                ->createItemButtonLabel(__('filament::admin.add_var'))
                                ->columns(1),
                        ]),


                        Step::make(__('filament::admin.images'))->schema([
                            FileUpload::make('images')
                                ->label(__('filament::admin.product_images'))
                                ->multiple()
                                ->disk('public')
                                ->directory('products')
                                ->image()
                                ->required()
                                ->preserveFilenames()


                                ->afterStateHydrated(function ($set, $record) {
                                    if ($record) {
                                        // عرض الصور القديمة عند Edit
                                        $set('images', $record->images->pluck('path')->toArray());
                                    }
                                })
                                ->saveUploadedFileUsing(
                                    fn($file) =>
                                    $file->storeAs(
                                        'products',
                                        'product_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension(),
                                        'public'
                                    )
                                )
                                ->saveRelationshipsUsing(function ($state, $record) {
                                    // نحذف القديم ونحفظ الجديد
                                    $record->images()->delete();

                                    foreach ($state as $path) {
                                        $record->images()->create([
                                            'product_id' => $record->id,
                                            'path' => $path,
                                        ]);
                                    }
                                }),


                        ]),



                        Step::make(__('filament::admin.data_sheets'))->schema([

                            Repeater::make('dataSheets')
                                ->label(__('filament::admin.data_sheets_manager')) // تسمية جديدة لإدارة الصفحات
                                ->relationship('dataSheets') // الربط بعلاقة dataSheets مباشرة
                                ->minItems(0) // للسماح بحفظ المنتج بدون صفحات بيانات
                                ->defaultItems(0)
                                ->columnSpanFull()
                                ->schema([

                                    // 1. حقل رفع الملف: يجب أن يكون اسمه اسم العمود الذي يخزن المسار في جدول data_sheets
                                    FileUpload::make('file_path')
                                        ->label(__('products.data_sheet_file')) // ملف الصفحة
                                        ->disk('public')
                                        ->directory('products_data_sheets')
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->required()
                                        ->preserveFilenames()
                                        ->columnSpan(1)
                                        // الحل: فحص قيمة الـ $file لتجنب خطأ الـ null في صفحة التعديل
                                        ->saveUploadedFileUsing(
                                            fn($file) => $file
                                                ? $file->storeAs(
                                                    'products_data_sheets',
                                                    'product_data_sheet_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension(),
                                                    'public'
                                                )
                                                : null
                                        ),

                                    // 2. حقول الترجمة: استخدام Tabs داخل Repeater لإدخال الاسم متعدد اللغات
                                    Tabs::make('Translations')
                                        ->columnSpan(2)
                                        ->tabs([
                                            Tab::make('English')
                                                ->schema([
                                                    TextInput::make('name.en') // يُفترض أن 'name' هو عمود JSON أو Translatable
                                                        ->label('Name (English)')
                                                        ->required()
                                                        ->maxLength(255),
                                                ]),
                                            Tab::make('العربية')
                                                ->schema([
                                                    TextInput::make('name.ar')
                                                        ->label('الاسم (بالعربية)')
                                                        ->required()
                                                        ->maxLength(255),
                                                ]),
                                        ]),
                                ])
                                ->columns(3) // لتوزيع الحقول داخل الـ Repeater: عمود للملف وعمودين للترجمة
                                ->addActionLabel(__('products.add_new_data_sheet')), // تسمية زر الإضافة

                        ]),

                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament::admin.name'))
                    ->formatStateUsing(fn($state, $record) => $record->getTranslation('name', app()->getLocale()))
                    ->searchable()
                    ->limit(20)
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('filament::admin.category'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand.name')
                    ->label(__('filament::admin.brand'))
                    ->sortable(),

                Tables\Columns\IconColumn::make('has_variants')
                    ->label(__('filament::admin.has_var'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('base_price')
                    ->label(__('filament::admin.base_price'))
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        // 💡 التحقق المباشر من أن المنتج يحتوي على خيارات
                        if ($record->has_variants) {
                            // استخدام الترجمة لـ 'has variants'
                            return __('filament::admin.has_variants');
                        }

                        // إذا لم يكن هناك خيارات، نعود لعرض السعر
                        if (isset($record->base_price)) {
                            // نتحقق أيضًا من وجود كود العملة لتجنب الأخطاء
                            $currencyCode = $record->currency->code ?? '';
                            return number_format($record->base_price, 2) . ' ' . $currencyCode;
                        }

                        // إذا لم يكن هناك خيارات ولا سعر أساسي (وهذا نادر)
                        return 'N/A';
                    }),

            ])

            ->filters([
                SelectFilter::make('currency')
                    ->label('Currency')
                    ->relationship('currency', 'code')

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make()

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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                Section::make(__('filament::admin.basic_info'))
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('filament::admin.product_name'))
                            ->size('lg')
                            ->weight('bold'),

                        TextEntry::make('description')
                            ->label(__('filament::admin.description'))
                            ->columnSpanFull()
                            ->markdown(),
                        TextEntry::make('has_variants')
                            ->label(__('filament::admin.variants'))
                            ->getStateUsing(function ($record) {
                                return $record->has_variants ? 'Has Variants' : __('filament::admin.check_variants_exists');
                            })->badge(),
                        TextEntry::make('has_discount')
                            ->label(__('filament::admin.discount'))
                            ->getStateUsing(function ($record) {

                                return $record->has_discount // <--- Added 'return' here
                                    ? "Has discount " . $record->discount_percentage . "%"
                                    :  __('filament::admin.check_discount_exists');
                            })->badge(),
                        TextEntry::make('highlights')
                            ->label(__('filament::admin.highlights'))
                            ->getStateUsing(function ($record) {

                                return $record->highlights // <--- Added 'return' here
                                    ? $record->highlights
                                    : __('filament::admin.check_highlights_exists');
                            })->markdown()->columnSpanFull(),
                        TextEntry::make('drawbacks')
                            ->label(__('filament::admin.drawbacks'))
                            ->columnSpanFull()
                            ->getStateUsing(function ($record) {

                                return $record->drawbacks // <--- Added 'return' here
                                    ? $record->drawbacks
                                    : __('filament::admin.check_drawbacks_exists');
                            })->markdown(),
                    ])
                    ->columns(2),

                Section::make(__('filament::admin.product_details'))
                    ->schema([
                        // ✅ آمن بفضل Eager Loading
                        TextEntry::make('brand.name')
                            ->label(__('filament::admin.product_brand'))
                            ->size('lg')
                            ->weight('bold'),

                        // ✅ آمن بفضل Eager Loading
                        TextEntry::make('category.name')
                            ->label(__('filament::admin.product_category'))
                            ->columnSpanFull()
                            ->markdown(),
                    ])
                    ->columns(2),



                Section::make(__('filament::admin.variants_attributes'))
                    ->schema([
                        RepeatableEntry::make('variants')
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('filament::admin.name'))
                                    ->formatStateUsing(
                                        fn($state, $record) =>
                                        $record->getTranslation('name', app()->getLocale())
                                    ),

                                TextEntry::make('price')
                                    ->label(__('filament::admin.price'))
                                    ->formatStateUsing(
                                        fn($state, $record) =>
                                        // ✅ آمن بفضل Eager Loading
                                        number_format($state, 2) . ' ' . ($record->product->currency->code ?? '')
                                    ),

                                TextEntry::make('final_price')
                                    ->label(__('filament::admin.final_price'))
                                    ->state(function ($record) {

                                        $discount = $record->product->discount_percentage ?? 0;
                                        return $discount > 0
                                            ? $record->price * (1 - ($discount / 100))
                                            : $record->price;
                                    })
                                    ->formatStateUsing(
                                        fn($state, $record) =>
                                        // ✅ آمن بفضل Eager Loading
                                        number_format($state, 2) . ' ' . ($record->product->currency->code ?? '')
                                    )->visible(fn($record) => $record->product->has_discount),

                                TextEntry::make('quantity')
                                    ->label(__('filament::admin.stock')),


                                TextEntry::make('highlights')
                                    ->label(__('filament::admin.highlights'))
                                    ->formatStateUsing(
                                        fn($state, $record) => $record->highlights ? $record->highlights : __('filament::admin.check_highlights_exists')

                                    )->markdown()
                                    ->visible(fn($record) => $record->highlights)
                                    ->columnSpanFull(),



                                // الكود المقترح والأكثر نظافة:
                                TextEntry::make('drawbacks')
                                    ->label(__('filament::admin.drawbacks'))
                                    ->formatStateUsing(
                                        fn($state) =>
                                        $state // Filament يمرر قيمة حقل 'drawbacks' إلى $state
                                            ? $state
                                            : __('filament::admin.check_drawbacks_exists') // تصحيح إملائي بسيط لـ "There are..."
                                    )
                                    ->markdown()
                                    ->visible(fn($record) => $record->drawbacks)
                                    ->columnSpanFull(),
                                Section::make(__('filament::admin.variants_images'))
                                    ->schema([
                                        ImageEntry::make('variantImages')
                                            ->label(__('filament::admin.product_images'))
                                            ->circular(false)
                                            ->columnSpanFull()
                                            ->square()
                                            ->limit(9)
                                            ->extraAttributes([
                                                'class' => 'grid grid-cols-3 gap-4',
                                            ])
                                            ->getStateUsing(
                                                fn($record) =>
                                                // إذا كان $record هو المنتج ولديه علاقة hasMany Variants
                                                // يجب عليك تجميع كل صور الخيارات هنا
                                                // لكن بافتراض أن $record هو الخيار ولديه علاقة variantImages
                                                $record->variantImages
                                                    ->pluck('path')
                                                    ->map(fn($path) => asset('storage/' . $path))
                                                    ->toArray()
                                            )

                                    ])
                                    // 💡 التحقق في visible صحيح، لكن يمكنك تبسيطه
                                    ->visible(
                                        fn($record) =>
                                        $record->product->has_variants && $record->variantImages->isNotEmpty()
                                    ),
                                RepeatableEntry::make('attributeValues')
                                    ->label(__('filament::admin.attributes'))
                                    ->schema([
                                        // ✅ آمن بفضل Eager Loading
                                        TextEntry::make('attribute.name')
                                            ->label(__('filament::admin.attribute')),

                                        TextEntry::make('value')
                                            ->label(__('filament::admin.value'))
                                            ->badge()
                                            ->color('info'),
                                    ])
                                    ->columns(2),
                            ])
                            ->columns(3),
                    ])
                    ->collapsible()
                    ->visible(fn($record) => $record->has_variants),



                Section::make(__('filament::admin.general_info'))
                    ->schema([
                        TextEntry::make('base_price')
                            ->label(__('filament::admin.base_price'))
                            ->formatStateUsing(
                                fn($state, $record) =>
                                // ✅ آمن بفضل Eager Loading
                                number_format($state, 2) . ' ' . ($record->currency->code ?? '')
                            )
                            ->weight('bold'),


                        TextEntry::make('discount_percentage')
                            ->label(__('filament::admin.discount_precentage'))
                            ->formatStateUsing(fn($state) => $state !== null ? intval($state) . '%' : '0%')
                            ->visible(fn($record) => $record->has_discount),

                        TextEntry::make('final_price')
                            ->label(__('filament::admin.price_after_discount'))
                            ->state(function ($record) {
                                return $record->discount_percentage
                                    ? $record->base_price * (1 - ($record->discount_percentage / 100))
                                    : $record->base_price;
                            })
                            ->formatStateUsing(
                                fn($state, $record) =>
                                // ✅ آمن بفضل Eager Loading
                                number_format($state, 2) . ' ' . ($record->currency->code ?? '')
                            )->visible(fn($record) => $record->has_discount),



                        TextEntry::make('quantity')
                            ->label(__('filament::admin.stock_quantity')),



                    ])
                    ->columns(2)
                    ->visible(fn($record) => ! $record->has_variants),

                Section::make(__('filament::admin.images'))
                    ->schema([
                        ImageEntry::make('images.path')
                            ->label(__('filament::admin.product_images'))
                            ->circular(false)
                            ->columnSpanFull()
                            ->square()
                            ->limit(9)
                            ->extraAttributes([
                                'class' => 'grid grid-cols-3 gap-4',
                            ])
                            ->getStateUsing(
                                fn($record) =>
                                $record->images->pluck('path')->map(fn($img) => asset('storage/' . $img))->toArray()
                            )
                            ->height(200)
                            ->width(200),
                    ])
                    ->collapsible(),
                Section::make(__('filament::admin.data_sheets'))
                    ->schema([
                        RepeatableEntry::make('dataSheets')
                            ->label(__('filament::admin.data_sheets_manager'))
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('filament::admin.name'))
                                    ->size('lg')
                                    ->weight('bold'),
                                TextEntry::make('file_path')
                                    ->label(__('filament::admin.data_sheet_file'))
                                    // 💡 تعديل النص الظاهر: لعرض كلمة ثابتة بدلاً من اسم الملف
                                    ->formatStateUsing(function ($state) {
                                        // إذا كان هناك مسار للملف ($state)، اعرض النص "عرض الملف"، وإلا اعرض "غير متوفر"
                                        return $state ? 'عرض الملف' : 'غير متوفر';
                                    })
                                    // 🔗 الحفاظ على الرابط: يتم إنشاء الرابط باستخدام القيمة الأصلية ($state)
                                    ->url(fn($state) => $state ? asset('storage/' . $state) : '#')
                                    ->openUrlInNewTab()
                                    ->badge()
                                    ->icon('heroicon-o-document-text'),
                            ])->columns(2)
                            ->visible(fn($record) => $record->dataSheets)
                            ->grid(2),
                    ])->visible(fn($record) => count($record->dataSheets) > 0)->collapsible(),
            ]);
    }
    public static function getEloquentQuery(): Builder
    {
        // الاستعلام الأساسي للصفحة الرئيسية للجدول (Table)
        $query = parent::getEloquentQuery()
            ->with(['currency', 'category', 'brand']); // لصفحات Table و Edit

        // تحسين خاص لصفحة العرض (View - Infolist) لتجنب N+1 في الـ RepeatableEntry
        if (request()->routeIs(static::getRouteBaseName() . '.view')) {
            $query->with([
                'images',
                'dataSheets',
                'variants.variantImages',
                // الأهم: لتحميل الـ Attribute المتداخل داخل Variant
                'variants.attributeValues.attribute',
            ]);
        }

        return $query;
    }


    public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.product_management');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.products'); // المسؤولين
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.products'); // مسؤول
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record:slug}/edit'),
            'view' => Pages\ViewProduct::route('/{record:slug}'), // هنا

        ];
    }
}
