<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_id')
                    ->label(__('filament::admin.user_name'))
                    ->state(function ($record) {
                        if (isset($record->user_id)) {
                            return $record->user->name;
                        } else {
                            return $record->order->f_name . ' ' . $record->order->l_name;
                        }
                    }),
                TextColumn::make('subtotal')
                    ->label(__('filament::admin.subtotal')),
                TextColumn::make('shipping_price')
                    ->label(__('filament::admin.shipping_price'))
                    ->state(function ($record) {
                        return $record->shipping_price . ' ' . $record->currency->code;
                    }),
                TextColumn::make('total_amount')
                    ->label(__('filament::admin.total_amount')),
                TextColumn::make('status')
                    ->label(__('filament::admin.status'))

            ])


            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make(__('filament::admin.user_details'))
                ->schema([
                    Grid::make(3)->schema([

                        TextEntry::make('user_name')
                            ->label(__('filament::admin.user_name'))
                            ->state(function ($record) {
                                if ($record->user_id) {
                                    return $record->user->name;
                                }

                                return $record->order->f_name . ' ' . $record->order->l_name;
                            }),

                        TextEntry::make('phone')
                            ->label(__('filament::admin.phone'))
                            ->state(fn($record) => $record->order->phone),
                        TextEntry::make('email')
                            ->label(__('filament::admin.email'))
                            ->state(fn($record) => $record->order->email),

                    ]),

                ])->collapsible(),
            Section::make(__('filament::admin.sales_items'))
                ->collapsible()


                ->schema([
                    RepeatableEntry::make('items')
                        ->label(__('filament::admin.items'))
                        ->schema([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('product_id')
                                        ->label(__('filament::admin.product'))
                                        ->state(function ($record) {
                                            $product = Product::find($record->product_id);
                                            return $product->getTranslation('name', app()->getLocale());
                                        }),
                                    TextEntry::make('product_variant_id')
                                        ->label(__('filament::admin.variant'))
                                        ->state(function ($record) {
                                            $variant = ProductVariant::find($record->product_variant_id);
                                            return $variant->getTranslation('name', app()->getLocale());
                                        })
                                        ->hidden(fn($record) => $record->product_variant_id === null),
                                ]),
                            Grid::make(3)->schema([
                                TextEntry::make('price')
                                    ->label(__('filament::admin.price'))
                                    ->state(function ($record) {
                                        return $record->price . ' ' . $record->currency->code;
                                    }),
                                TextEntry::make('quantity')
                                    ->label(__('filament::admin.quantity')),

                                TextEntry::make('total')
                                    ->label(__('filament::admin.total'))
                                    ->state(function ($record) {
                                        return $record->total . ' ' . $record->currency->code;
                                    }),
                            ])

                        ])
                ]),
            Section::make(__('filament::admin.blilling_details'))
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('subtotal')
                            ->label(__('filament::admin.subtotal'))
                            ->state(fn($record) => $record->subtotal . ' ' . $record->currency->code),
                        TextEntry::make('shipping_price')
                            ->label(__('filament::admin.shipping_price'))
                            ->state(fn($record) => $record->shipping_price . ' ' . $record->currency->code),
                        TextEntry::make('total_amount')
                            ->label(__('filament::admin.total_amount'))
                            ->state(fn($record) => $record->total_amount . ' ' . $record->currency->code)
                    ]),
                    Grid::make(3)->schema([
                        TextEntry::make('tax')
                            ->label(__('filament::admin.tax'))
                            ->state(fn($record) => $record->tax . ' ' . $record->currency->code),
                        TextEntry::make('discount')
                            ->label(__('filament::admin.discount'))
                            ->state(fn($record) => $record->discount . ' ' . $record->currency->code),
                        TextEntry::make('status')
                            ->label(__('filament::admin.status'))

                    ])

                ])->collapsible(),

        ]);
    }




    public static function getLabel(): ?string
    {
        return __('filament::admin.sale');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.sales');
    }
    public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.sales');
    }
    public static function getNavigationBadge(): ?string
    {
        return Sale::count();
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
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
            'view' => Pages\View::route('/{record}'),
        ];
    }
}
