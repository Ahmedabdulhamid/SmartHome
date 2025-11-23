<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static?int $navigationSort=2;
     protected static ?int $navigationGroupSort = 1;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('filament::admin.permission_name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('guard_name')
                    ->label(__('filament::admin.guard'))
                    ->options([
                        'admin' => __('filament::admin.admin'),
                        'web' =>  __('filament::admin.user'),
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
           ->columns([
            TextColumn::make('name')
                ->label(__('filament::admin.name'))

                ->searchable(),

            TextColumn::make('guard_name')
                ->label(__('filament::admin.guard'))
                ->sortable(),
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
    public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.admin_management');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.permissions'); // المسؤولين
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.permissions'); // مسؤول
    }
 public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
