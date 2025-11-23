<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Filament\Resources\CurrencyResource\RelationManagers;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->label(__('filament::admin.code'))
                    ->maxLength(3),

                Forms\Components\TextInput::make('name')
                    ->label(__('filament::admin.name'))
                    ->required(),

                Forms\Components\TextInput::make('symbol')
                    ->label(__('filament::admin.symbol'))
                    ->maxLength(10),

                Forms\Components\TextInput::make('precision')
                    ->numeric()
                    ->label(__('filament::admin.precesion'))
                    ->default(2),

                Forms\Components\TextInput::make('decimal_mark')
                    ->label(__('filament::admin.decimal_mark'))
                    ->default('.'),

                Forms\Components\TextInput::make('thousands_separator')
                    ->label(__('filament::admin.thousands_separator'))
                    ->default(','),

                Forms\Components\Toggle::make('symbol_first')
                    ->label(__('filament::admin.symbol_first'))
                    ->default(true),

                Forms\Components\Toggle::make('active')
                    ->label(__('filament::admin.active'))
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label(__('filament::admin.code'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->label(__('filament::admin.name'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('symbol')->label(__('filament::admin.symbol')),
                Tables\Columns\IconColumn::make('symbol_first')->label(__('filament::admin.symbol_first'))->boolean(),
                Tables\Columns\IconColumn::make('active')->label(__('filament::admin.active'))->boolean(),
                Tables\Columns\TextColumn::make('precision')->label(__('filament::admin.precesion')),
            ])
            ->filters([

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
        return __('filament::admin.currency_management');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.currencies'); // المسؤولين
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.currencies'); // مسؤول
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
