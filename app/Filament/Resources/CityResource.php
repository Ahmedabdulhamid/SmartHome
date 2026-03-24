<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

               Select::make('governorate_id')
               ->label(__('filament::admin.goveronrate'))
               ->relationship('governorate','name')
               ->required()
               ->searchable()
               ->live()
              ->preload()
              ->columnSpanFull(),
              Tabs::make()
              ->tabs([
                Tab::make(__('filament::admin.english'))
                ->schema([
                    Forms\Components\TextInput::make('name.en')
                    ->label(__('filament::admin.name'))
                    ->required()
                    ->maxLength(255),
                ]),
                Tab::make(__('filament::admin.arabic'))
                ->schema([
                    Forms\Components\TextInput::make('name.ar')
                    ->label(__('filament::admin.name'))
                    ->required()
                    ->maxLength(255),
                ]),
            ])->columnSpanFull(),
            Toggle::make('status')
            ->label(__('filament::admin.status'))
            ->required()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('filament::admin.name'))->sortable()->searchable(),
                TextColumn::make('governorate.name')->label(__('filament::admin.goveronrate'))->sortable()->searchable(),
                SelectColumn::make('status')->label(__('filament::admin.status'))->options([
                    'active' => __('filament::admin.active'),
                    'inactive' => __('filament::admin.inactive'),
                ])->sortable()->searchable(),
            ])
            ->filters([
               SelectFilter::make('governorate')->relationship('governorate', 'name')->label(__('filament::admin.goveronrate')),
                SelectFilter::make('status')->label(__('filament::admin.status'))->options([
                    'active' => __('filament::admin.active'),
                    'inactive' => __('filament::admin.inactive'),
                ]),
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
        return __('filament::admin.shipping_zones');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.cities');
    }
    public static function getLabel(): ?string
    {
        return __('filament::admin.city');
    }
    public static function getNavigationBadge(): ?string
    {
        return (string) City::count();
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
