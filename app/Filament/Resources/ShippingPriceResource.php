<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShippingPriceResource\Pages;
use App\Filament\Resources\ShippingPriceResource\RelationManagers;
use App\Models\City;
use App\Models\ShippingPrice;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShippingPriceResource extends Resource
{
    protected static ?string $model = ShippingPrice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make(__('filament::admin.title'))
                    ->schema([
                        Select::make('governorate_id')
                            ->label(__('filament::admin.goveronrate'))
                            ->relationship('governorate', 'name')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn(callable $set) => $set('city_id', null)),

                        Select::make('city_id')
                            ->label(__('filament::admin.city'))
                            ->options(function (callable $get) {
                                $governorate = $get('governorate_id');
                                return $governorate
                                    ? City::where('governorate_id', $governorate)->pluck('name', 'id')
                                    : [];
                            })
                            ->searchable()
                            ->required()
                            ->placeholder(__('filament::admin.select_city')),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('filament::admin.shipping_info'))
                    ->schema([

                        Select::make('currency_id')
                            ->label(__('filament::admin.currency'))
                            ->relationship('currency', 'code')
                            ->searchable()
                            ->required(),

                        Select::make('shipping_type')
                            ->label(__('filament::admin.shipping_type'))
                            ->options([
                                'standard' => __('filament::admin.standard'),
                                'express' => __('filament::admin.express'),
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('estimated_days')
                            ->label(__('filament::admin.estimated_days'))
                            ->numeric()
                            ->nullable(),

                        Forms\Components\TextInput::make('price')
                            ->label(__('filament::admin.price'))
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('return_fee')
                            ->label(__('filament::admin.return_fee'))
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),

                Forms\Components\Section::make(__('filament::admin.weight_range'))
                    ->schema([
                        Forms\Components\TextInput::make('min_weight')
                            ->label(__('filament::admin.min_weight'))
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Forms\Components\TextInput::make('max_weight')
                            ->label(__('filament::admin.max_weight'))
                            ->numeric()
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('governorate.name')->label(__('filament::admin.goveronrate'))
                ->sortable()->searchable(),
                Tables\Columns\TextColumn::make('city.name')->label(__('filament::admin.city'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('shipping_type')->label(__('filament::admin.shipping_type'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('price')->label(__('filament::admin.price'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('currency.code')->label(__('filament::admin.currency'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('estimated_days')->toggleable(isToggledHiddenByDefault:false)->label(__('filament::admin.estimated_days'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('min_weight')->toggleable(isToggledHiddenByDefault:false)->label(__('filament::admin.min_weight'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('max_weight')->toggleable(isToggledHiddenByDefault:false)->label(__('filament::admin.max_weight'))->sortable()->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                 Tables\Actions\DeleteAction::make(),
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
        return __('filament::admin.shipping_zones');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.shipping_prices');
    }
    public static function getLabel(): ?string
    {
        return __('filament::admin.shipping_price');
    }
    public static function getNavigationBadge(): ?string
    {
        return (string) ShippingPrice::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShippingPrices::route('/'),
            'create' => Pages\CreateShippingPrice::route('/create'),
            'edit' => Pages\EditShippingPrice::route('/{record}/edit'),
        ];
    }
}
