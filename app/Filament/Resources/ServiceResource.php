<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Currency;
use App\Models\Feature;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver'; // أيقونة Smart Home
    protected static ?string $navigationGroup = 'إدارة الخدمات والمنتجات';

    // =======================================================
    // ترجمة أسماء الـ Resource
    // =======================================================
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.plural'); // الخدمات
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.single'); // خدمة
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        // =======================================================
                        // 1. حقول الترجمة (العنوان والوصف القصير والوصف الكامل)
                        // =======================================================
                        Forms\Components\Section::make(__('filament::admin.content_section_title'))
                            ->description(__('filament::admin.content_section_description'))
                            ->schema([
                                Forms\Components\Tabs::make('Translations Title')
                                    ->tabs([
                                        Forms\Components\Tabs\Tab::make('English')
                                            ->schema([
                                                TextInput::make('title.en')
                                                    ->label(__('filament::admin.title_en'))
                                                    ->unique(table: 'services', column: 'title->en', ignoreRecord: true)
                                                    ->required()
                                                    ->maxLength(255),
                                            ]),
                                        Forms\Components\Tabs\Tab::make(__('filament::admin.arabic_tab_title'))
                                            ->schema([
                                                TextInput::make('title.ar')
                                                    ->label(__('filament::admin.title_ar'))
                                                    ->required()
                                                    ->unique(table: 'services', column: 'title->ar', ignoreRecord: true)
                                                    ->maxLength(255),
                                            ]),
                                    ]),

                                Forms\Components\Tabs::make('Translations Short Description')
                                    ->tabs([
                                        Forms\Components\Tabs\Tab::make('English')
                                            ->schema([
                                                Forms\Components\Textarea::make('short_description.en')
                                                    ->label(__('filament::admin.short_desc_en'))
                                                    ->required()
                                                    ->rows(3),
                                            ]),
                                        Forms\Components\Tabs\Tab::make(__('filament::admin.arabic_tab_title'))
                                            ->schema([
                                                Forms\Components\Textarea::make('short_description.ar')
                                                    ->label(__('filament::admin.short_desc_ar'))
                                                    ->required()
                                                    ->rows(3),
                                            ]),
                                    ]),

                                Forms\Components\Tabs::make('Translations Full Description')
                                    ->tabs([
                                        Forms\Components\Tabs\Tab::make('English')
                                            ->schema([
                                                Forms\Components\RichEditor::make('description.en')
                                                    ->label(__('filament::admin.full_desc_en'))
                                                    ->required()
                                                    ->fileAttachmentsDisk('public')
                                                    ->fileAttachmentsDirectory('service-attachments'),
                                            ]),
                                        Forms\Components\Tabs\Tab::make(__('filament::admin.arabic_tab_title'))
                                            ->schema([
                                                Forms\Components\RichEditor::make('description.ar')
                                                    ->label(__('filament::admin.full_desc_ar'))
                                                    ->required()
                                                    ->fileAttachmentsDisk('public')
                                                    ->fileAttachmentsDirectory('service-attachments'),
                                            ]),
                                    ]),
                            ])->columns(1),

                        // =======================================================
                        // 2. حقول الـ SEO
                        // =======================================================
                        Forms\Components\Section::make(__('filament::admin.seo_section_title'))
                            ->collapsed()
                            ->schema([
                                Forms\Components\Tabs::make('Meta Description')
                                    ->tabs([
                                        Forms\Components\Tabs\Tab::make('English')
                                            ->schema([
                                                Forms\Components\Textarea::make('meta_description.en')
                                                    ->label(__('filament::admin.meta_description_en'))
                                                    ->rows(2)
                                                    ->maxLength(160)
                                                    ->nullable(),
                                            ]),
                                        Forms\Components\Tabs\Tab::make(__('filament::admin.arabic_tab_title'))
                                            ->schema([
                                                Forms\Components\Textarea::make('meta_description.ar')
                                                    ->label(__('filament::admin.meta_description_ar'))
                                                    ->rows(2)
                                                    ->maxLength(160)
                                                    ->nullable(),
                                            ]),
                                    ])->columnSpanFull(),
                            ]),

                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        // =======================================================
                        // 3. الإعدادات الأساسية
                        // =======================================================
                        Forms\Components\Section::make(__('filament::admin.settings_section_title'))
                            ->schema([
                                Select::make('category_id')
                                    ->label(__('filament::admin.category'))
                                    ->relationship('category', 'name', fn($query) => $query->where('type', 'services'))
                                    ->getOptionLabelFromRecordUsing(fn($record) => $record->getTranslation('name', app()->getLocale()))
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\FileUpload::make('icon')
                                    ->label(__('filament::admin.icon'))
                                    ->image()
                                    ->disk('public')
                                    ->directory('icon')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('1080')
                                    ->imageResizeTargetHeight('607')
                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                                        $newName = 'icon_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                                        return $file->storeAs('icons', $newName, 'public');
                                    })
                                    ->required(),

                                Forms\Components\Toggle::make('is_active')
                                    ->label(__('filament::admin.is_active'))
                                    ->default(true),

                                Forms\Components\TextInput::make('sort_order')
                                    ->label(__('filament::admin.sort_order'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(1),

                                Forms\Components\FileUpload::make('image')
                                    ->label(__('filament::admin.service_image'))
                                    ->image()
                                    ->disk('public')
                                    ->directory('services')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('1080')
                                    ->imageResizeTargetHeight('607')
                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                                        $newName = 'service_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                                        return $file->storeAs('services', $newName, 'public');
                                    })
                                    ->required(),
                                ]), // الإعدادات الأساسية عمود واحد

                        // =======================================================
                        // 4. التسعير المتعدد العملات
                        // =======================================================
                        Forms\Components\Section::make(__('filament::admin.pricing_section_title'))
                            ->schema([
                                TextInput::make('base_price')
                                    ->label(__('filament::admin.base_price'))
                                    ->numeric()
                                    ->rules(['regex:/^\d{1,8}(\.\d{1,2})?$/'])
                                    ->required(),

                                Select::make('currency_id')
                                    ->label(__('filament::admin.base_currency'))
                                    ->relationship('baseCurrency', 'name') // العلاقة baseCurrency في Service Model
                                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name . ' (' . $record->code . ')')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ])

                    ])->columnSpanFull(),


                Section::make(__('filament::admin.features_section_title'))
                    ->description(__('filament::admin.features_section_description'))
                    ->collapsed()
                    ->schema([

                        Repeater::make('feature_pivot_data')
                            ->label(__('filament::admin.features_list'))

                            ->schema([
                                Select::make('feature_id')
                                    ->label(__('filament::admin.feature'))
                                    ->required()
                                    ->options(Feature::all()->mapWithKeys(fn($feature) => [
                                        $feature->id => $feature->getTranslation('name', app()->getLocale())
                                    ])->toArray())
                                    ->searchable()
                                    ->preload()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),


                                TextInput::make('additional_cost')
                                    ->label(__('filament::admin.additional_cost'))
                                    ->numeric()
                                    ->nullable()
                                    ->default(10.00),

                                Select::make('currency_id')
                                    ->label(__('filament::admin.additional_cost_currency'))
                                    ->required()
                                    ->options(Currency::all()->mapWithKeys(fn($currency) => [
                                        $currency->id => $currency->name . ' (' . $currency->code . ')'
                                    ])->toArray())
                                    ->searchable()
                                    ->preload(),
                            ])->columns(3)
                            ->maxItems(10)
                            ->defaultItems(0)
                            ->createItemButtonLabel(__('filament::admin.add_new_feature')),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('filament::admin.image_column_title'))
                    ->disk('public')
                    ->width(80)
                    ->height(80)
                    ->rounded(),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament::admin.title_column'))
                    ->getStateUsing(fn($record) => $record->getTranslation('title', app()->getLocale()))
                    ->searchable()
                    ->sortable(),

                // عمود جديد لعرض الميزات المرتبطة
                Tables\Columns\TextColumn::make('features.name')
                    ->label(__('filament::admin.features_column'))
                    ->listWithLineBreaks()
                    ->bulleted()
                    ->limit(50),

                Tables\Columns\ImageColumn::make('icon')
                    ->label(__('filament::admin.icon_column'))
                    ->disk('public')
                    ->width(30)
                    ->height(30)
                    ->rounded(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('filament::admin.category_column'))
                    ->getStateUsing(fn($record) => $record->category?->getTranslation('name', app()->getLocale()))
                    ->sortable(),

                Tables\Columns\TextColumn::make('base_price')
                    ->label(__('filament::admin.base_price_column'))
                    ->formatStateUsing(fn($state, Service $record) => $state . ' ' . $record->baseCurrency?->code)
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('filament::admin.is_active_column'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label(__('filament::admin.sort_order_column'))
                    ->sortable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label(__('filament::admin.filter_by_category'))
                    ->relationship('category', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->getTranslation('name', app()->getLocale()))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            // سنضيف هنا أي علاقات مستقبلية مثل جدول الـ Reviews أو الـ FAQs
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.services_management');
    }


    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
