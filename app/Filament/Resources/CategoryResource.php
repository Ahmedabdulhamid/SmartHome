<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;


    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 2;
 protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Translations')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('English')
                            ->schema([
                                Forms\Components\TextInput::make('name.en')
                                    ->label('Name (English)')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(
                                        table: 'categories',
                                        column: 'name->en', // التحقق على مسار JSON
                                        ignoreRecord: true,
                                        modifyRuleUsing: function (Forms\Components\TextInput $component, Unique $rule) {
                                            $typeValue = $component->getContainer()->getRawState()['type'] ?? null;

                                            if (!$typeValue && $component->getRecord()) {
                                                $typeValue = $component->getRecord()->type;
                                            }

                                            if ($typeValue) {
                                                // إضافة شرط النوع (type) لقاعدة الفرادة (Unique Rule)
                                                return $rule->where('type', $typeValue);
                                            }
                                            return $rule;
                                        }
                                    ),
                            ]),
                        Forms\Components\Tabs\Tab::make('العربية')
                            ->schema([
                                Forms\Components\TextInput::make('name.ar')
                                    ->label('الاسم (بالعربية)')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(
                                        table: 'categories',
                                        column: 'name->ar', // التحقق على مسار JSON
                                        ignoreRecord: true,
                                        modifyRuleUsing: function (Forms\Components\TextInput $component, Unique $rule) {
                                            $typeValue = $component->getContainer()->getRawState()['type'] ?? null;

                                            if (!$typeValue && $component->getRecord()) {
                                                $typeValue = $component->getRecord()->type;
                                            }

                                            if ($typeValue) {
                                                // إضافة شرط النوع (type) لقاعدة الفرادة (Unique Rule)
                                                return $rule->where('type', $typeValue);
                                            }
                                            return $rule;
                                        }
                                    ),
                            ]),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Select::make('type')
                    ->label(__('filament::admin.type'))
                    ->options([
                        'products' => __('filament::admin.products'),
                        "blogs" => __('filament::admin.blogs'),
                        "services" => __('filament::admin.services')
                    ])
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('parent_id')
                    ->label('Parent Category')
                    ->relationship('parent', 'id')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->getTranslation('name', app()->getLocale()))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->columnSpanFull(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament::admin.name'))
                    ->getStateUsing(
                        fn($record) => $record->getTranslation('name', App::getLocale())
                    )
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('filament::admin.type'))

                    ->searchable()
                    ->sortable(),


                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament::admin.created_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('filament::admin.type'))
                    ->options([
                        'products' => __('filament::admin.products'),
                        "blogs" => __('filament::admin.blogs'),
                        "services" => __('filament::admin.services')
                    ])
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


    // -------------------
    // PAGES
    // -------------------

    public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.product_management');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.categories');
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.categories');
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record:slug}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['parent']);
    }
}
