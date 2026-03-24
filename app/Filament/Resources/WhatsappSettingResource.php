<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WhatsappSettingResource\Pages;
use App\Filament\Resources\WhatsappSettingResource\RelationManagers;
use App\Models\WhatsappSetting;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WhatsappSettingResource extends Resource
{
    protected static ?string $model = WhatsappSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('meta_app_id')
                ->label('meta app id'),
                TextInput::make('phone_number_id')
                ->label('phone number id'),
                Textarea::make('meta_access_token')
                 ->rows(10)
                ->label('meta access token'),
                TextInput::make('meta_verify_token')
                ->label('meta verify token'),
                 TextInput::make('whatsapp_business_account_id')
                ->label('whatsapp business account id'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('meta_app_id')
                ->label('meta app id'),
                TextColumn::make('phone_number_id')
                ->label('phone number id'),
                TextColumn::make('meta_access_token')
                ->label('meta access token'),
                TextColumn::make('meta_verify_token')
                ->label('meta verify token'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWhatsappSettings::route('/'),
            'create' => Pages\CreateWhatsappSetting::route('/create'),
            'edit' => Pages\EditWhatsappSetting::route('/{record}/edit'),
        ];
    }
}
