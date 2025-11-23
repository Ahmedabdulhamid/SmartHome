<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxResource\Pages;
use App\Filament\Resources\TaxResource\RelationManagers;
use App\Models\Tax;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaxResource extends Resource
{
    protected static ?string $model = Tax::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('filament::admin.tax_name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('type')
                    ->label(__('filament::admin.type'))
                    ->placeholder('مثال: VAT, Sales, Withholding')
                    ->maxLength(255),

                Forms\Components\TextInput::make('rate')
                    ->label(__('filament::admin.rate'))
                    ->numeric()
                    ->required()
                    ->suffix('%'),

                Forms\Components\Toggle::make('is_active')
                    ->label(__('filament::admin.is_active'))
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
       return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament::admin.tax_name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('filament::admin.type'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('rate')
                    ->label(__('filament::admin.rate'))

                    ->sortable()
                     ->formatStateUsing(fn ($state) => $state !== null ? intval($state) . '%' : '0%'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label(__('filament::admin.is_active')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
        return __('filament::admin.tax_management');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.taxes'); // المسؤولين
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.taxes'); // مسؤول
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaxes::route('/'),
            'create' => Pages\CreateTax::route('/create'),
            'edit' => Pages\EditTax::route('/{record}/edit'),
        ];
    }
}
