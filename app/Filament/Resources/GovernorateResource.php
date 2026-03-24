<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GovernorateResource\Pages;

use App\Models\Governorate;

use Filament\Forms;
use Filament\Forms\Components\Select;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GovernorateResource extends Resource
{
    protected static ?string $model = Governorate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Translation')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('English')
                            ->schema([
                                TextInput::make('name.en')
                                    ->label('Name (English)')
                                    ->required()
                                    ->maxLength(255)
                                    ->rule('unique:governorates,name->en')
                            ]),
                        Forms\Components\Tabs\Tab::make('Arabic')
                            ->schema([
                                TextInput::make('name.ar')

                                    ->label('Name (Arabic)')
                                    ->required()
                                    ->maxLength(255)
                                    ->rule('unique:governorates,name->ar')
                            ])
                    ])->columnSpanFull(),
                Select::make('status')
                    ->label(__('filament::admin.status'))
                    ->required()
                    ->options([
                        'active' => __('filament::admin.active'),
                        'inactive' => __('filament::admin.inactive')
                    ])
                    ->columnSpanFull()

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label(__('filament::admin.name')),
                TextColumn::make('status')
                ->label(__('filament::admin.status'))
                ->getStateUsing(fn($record)=>$record->status=='active'?__('filament::admin.active'):__('filament::admin.inactive'))
            ])
            ->filters([
                SelectFilter::make('status')
               ->options([
                        'active' => __('filament::admin.active'),
                        'inactive' => __('filament::admin.inactive')
                    ])
                ->label(__('filament::admin.status'))

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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

    public static function getLabel(): ?string
    {
        return __('filament::admin.goveronrate');

    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.governorates');
    }
    public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.shipping_zones');
    }
    public static function getNavigationBadge(): ?string
    {
        return Governorate::count();
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGovernorates::route('/'),
            'create' => Pages\CreateGovernorate::route('/create'),
            'edit' => Pages\EditGovernorate::route('/{record}/edit'),
        ];
    }

}
