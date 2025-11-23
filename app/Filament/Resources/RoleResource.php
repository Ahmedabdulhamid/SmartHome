<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 3;
     protected static ?int $navigationGroupSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('filament::admin.name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('guard_name')
                    ->label(__('filament::admin.guard'))
                    ->options([
                        'admin' => __('filament::admin.admin'),
                        'web' =>  __('filament::admin.user'),
                    ])
                    ->required(),

                Forms\Components\MultiSelect::make('permissions')
                    ->relationship('permissions', 'name')
                    ->label(__('filament::admin.permissions'))
                    ->searchable()->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament::admin.name'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('guard_name')
                    ->label(__('filament::admin.guard'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label(__('filament::admin.permissions')),
            ])
            ->filters([
                //
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
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.admin_management');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.roles'); // المسؤولين
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.roles'); // مسؤول
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
