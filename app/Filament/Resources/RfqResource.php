<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RfqResource\Pages;
use App\Filament\Resources\RfqResource\RelationManagers;
use App\Models\Rfq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\RepeatableEntry;

class RfqResource extends Resource
{
    protected static ?string $model = Rfq::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('التليفون'),
                Tables\Columns\TextColumn::make('email')->label('البريد'),
                Tables\Columns\TextColumn::make('status')->label('status'),
                Tables\Columns\TextColumn::make('expected_price')->label('السعر المتوقع'),
                Tables\Columns\TextColumn::make('created_at')->toggleable(isToggledHiddenByDefault: true)->label('تاريخ الإنشاء')->date(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => "Pending",
                        'replied' => "Relied"
                    ])
            ])
            ->actions([

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
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([


            Section::make(__('filament::admin.rfq'))
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('name')
                                ->label(__('filament::admin.name')),

                            TextEntry::make('email')
                                ->label(__('filament::admin.email')),

                            TextEntry::make('phone')
                                ->label(__('filament::admin.phone')),

                            TextEntry::make('expected_price')
                                ->label(__('filament::admin.expected_price'))
                                ->visible(fn($record) => $record->expected_price)
                                ->formatStateUsing(function ($state, $record) {

                                    return number_format($state, 2) . ' ' . $record->currency->code;
                                }),

                            TextEntry::make('description')
                            ->label(__('filament::admin.description'))
                            ->visible(fn($record)=>$record->description)
                        ]),
                ]),

            // --- RFQ Details Section ---
            Section::make(__('filament::admin.rfq_details'))
                ->schema([
                    RepeatableEntry::make('items')
                    ->label(__('filament::admin.items'))
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    TextEntry::make('name')
                                        ->label(__('filament::admin.product'))
                                        ->getStateUsing(
                                            fn($record) =>
                                            $record->product->getTranslation('name', app()->getLocale())
                                        ),

                                    TextEntry::make('expected_price')
                                        ->label(__('filament::admin.price'))
                                        ->formatStateUsing(function ($state, $record) {
                                            return number_format($state, 2) . ' ' . $record->rfq->currency->code;
                                        }),

                                    TextEntry::make('variant_name')
                                        ->label(__('filament::admin.variant'))
                                        ->getStateUsing(
                                            fn($record) =>
                                            $record->variant->getTranslation('name', app()->getLocale())
                                        )
                                        ->visible(fn($record) => $record->variant),
                                ]),
                        ]),
                ])
                ->visible(fn($record) => count($record->items) > 0),
        ]);
    }




    public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.rfq_management');
    }

    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.rfqs'); // عروض الأسعار
    }
    public static function getLabel(): ?string
    {
        return __('filament::admin.rfqs'); // عرض سعر
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['items.product', 'items.variant', 'currency']); // eager load
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRfqs::route('/'),
            'create' => Pages\CreateRfq::route('/create'),
            'edit' => Pages\EditRfq::route('/{record}/edit'),
            'view' => Pages\ViewRfq::route('{record:id}')

        ];
    }
}
