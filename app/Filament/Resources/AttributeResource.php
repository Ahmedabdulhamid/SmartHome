<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttributeResource\Pages;
use App\Filament\Resources\AttributeResource\RelationManagers;
use App\Models\Attribute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Tabs;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('filament::admin.create_attr'))
                    ->schema([
                        Tabs::make('Languages')
                            ->tabs([
                                Tabs\Tab::make(__('filament::admin.arabic'))
                                    ->schema([
                                        TextInput::make('name.ar')
                                            ->label(__('filament::admin.name_arabic'))
                                            ->required()
                                            ->columnSpanFull(),


                                    ])
                                    ->columns(1),

                                Tabs\Tab::make(__('filament::admin.english'))
                                    ->schema([
                                        TextInput::make('name.en')
                                            ->label(__('filament::admin.name_english'))
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
                Repeater::make(__('filament::admin.values'))
                    ->relationship('values')
                    ->schema([


                        TextInput::make('value')
                            ->label(__('filament::admin.value'))
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->minItems(1)
                    ->createItemButtonLabel(__('filament::admin.add_value'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('filament::admin.name'))
                ->getStateUsing(fn($record) => $record->getTranslation('name', app()->getLocale())),
                Tables\Columns\TextColumn::make('values.value')->label(__('filament::admin.value')),

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
        return __('filament::admin.product_management');
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
     public static function getPluralLabel(): ?string
    {
        return __('filament::admin.attributes'); // المسؤولين
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.attributes'); // مسؤول
    }
    // 🌟 التعديل المقترح: أضف هذه الدالة إلى AttributeResource
public static function getEloquentQuery(): Builder
{
    // تحميل علاقة 'values' مسبقًا
    return parent::getEloquentQuery()->with(['values']);
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttributes::route('/'),
            'create' => Pages\CreateAttribute::route('/create'),
            'edit' => Pages\EditAttribute::route('/{record}/edit'),
        ];
    }
}
