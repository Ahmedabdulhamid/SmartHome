<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Filament\Actions\ForceDeleteAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Resources\Components\Tab;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ForceDeleteAction as ActionsForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

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
                TextColumn::make('f_name')
                    ->label(__('filament::admin.f_name')),
                TextColumn::make('l_name')
                    ->label(__('filament::admin.l_name')),
                TextColumn::make('total_amount')
                    ->label(__('filament::admin.total_amount')),
                TextColumn::make('governorate.name')
                    ->label(__('filament::admin.governorate')),
                TextColumn::make('city.name')
                    ->label(__('filament::admin.city')),
                SelectColumn::make('status')
                ->label(__('filament::admin.order_status'))
                    ->options([
                        'pending' => __('filament::admin.pending'),
                        'confirmed' => __('filament::admin.confirmed'),
                        'shipped' => __('filament::admin.shipped'),
                        'delivered' => __('filament::admin.delivered'),
                        'cancelled' => __('filament::admin.cancelled')
                    ]),
                TextColumn::make('created_at')
                    ->dateTime(),



            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => __('filament::admin.pending'),
                        'confirmed' => __('filament::admin.confirmed'),
                        'shipped' => __('filament::admin.shipped'),
                        'delivered' => __('filament::admin.shipped'),
                        'cancelled' => __('filament::admin.cancelled')
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Section::make(__('filament::admin.customer_info'))
                ->description(__('filament::admin.basic_customer_details'))
                ->collapsible()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('f_name')->label(__('filament::admin.f_name')),
                            TextEntry::make('l_name')->label(__('filament::admin.l_name')),
                        ]),

                    Grid::make(2)
                        ->schema([
                            TextEntry::make('email')->label(__('filament::admin.email')),
                            TextEntry::make('phone')->label(__('filament::admin.phone')),
                        ]),
                ]),

            Section::make(__('filament::admin.shipping_information'))
                ->description(__('filament::admin.shipping_info_region'))
                ->collapsible()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('governorate.name')->label(__('filament::admin.governorate')),
                            TextEntry::make('city.name')->label(__('filament::admin.city')),
                        ]),

                    Grid::make(1)
                        ->schema([
                            TextEntry::make('address')->label(__('filament::admin.address')),
                        ]),

                    Grid::make(2)
                        ->schema([
                            TextEntry::make('zip_code')->label(__('filament::admin.zip_code')),
                            TextEntry::make('shipping_price')->label(__('filament::admin.shipping_price')),
                        ]),
                ]),

            Section::make(__('filament::admin.payment_details'))
                ->description(__('filament::admin.order_amount_payment_status'))

                ->collapsible()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('total_amount')
                                ->label(__('filament::admin.total_amount'))
                                ->state(fn($record) => $record->total_amount . ' ' . $record->currency->code)
                                ->badge()
                                ->color('success'),

                            TextEntry::make('status')
                                ->label(__('filament::admin.order_status'))
                                ->badge()
                                ->color(fn($state) => match ($state) {
                                    'pending' => 'warning',
                                    'paid' => 'success',
                                    'canceled' => 'danger',
                                    default => 'gray',
                                }),
                        ]),
                ]),
            Section::make(__('filament::admin.order_items'))
                ->description(__('filament::admin.products_included'))
                ->collapsible()
                ->schema([

                    RepeatableEntry::make('items')
                        ->label('Order Items')
                        ->schema([

                            Section::make()
                                ->compact()
                                ->schema([

                                    Grid::make(3)
                                        ->schema([

                                            // Product Name
                                            TextEntry::make('product_id')
                                                ->label(__('filament::admin.product'))
                                                ->state(
                                                    fn($record) =>
                                                    Product::find($record->product_id)?->getTranslation('name', app()->getLocale())
                                                )
                                                ->weight('bold')
                                                ->color('primary'),

                                            // Variant Name (if exists)
                                            TextEntry::make('product_variant_id')
                                                ->label(__('filament::admin.variant'))
                                                ->state(
                                                    fn($record) =>
                                                    ProductVariant::find($record->product_variant_id)?->getTranslation('name', app()->getLocale())
                                                )
                                                ->hidden(fn($record) => $record->product_variant_id == null)
                                                ->color('warning'),

                                            // Quantity
                                            TextEntry::make('quantity')
                                                ->label(__('filament::admin.quantity'))
                                                ->badge()
                                                ->color('info'),
                                        ]),

                                    Grid::make(2)
                                        ->schema([

                                            // Price
                                            TextEntry::make('price')
                                                ->label(__('filament::admin.unit_price'))
                                                ->state(
                                                    fn($record) =>
                                                    number_format($record->price, 2) . ' ' . $record->currency->code
                                                )
                                                ->badge()
                                                ->color('success'),

                                            // Total
                                            TextEntry::make('total')
                                                ->label(__('filament::admin.total'))
                                                ->state(
                                                    fn($record) =>
                                                    number_format($record->total, 2) . ' ' . $record->currency->code
                                                )
                                                ->badge()
                                                ->color('danger')
                                                ->weight('bold'),
                                        ]),
                                ])
                                ->columnSpanFull()
                                ->compact(), // شكله يبقى Card صغير جميل

                        ])
                        ->columnSpanFull()
                ])

        ]);
    }
    public static function getLabel(): ?string
    {
        return __('filament::admin.order');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.orders');
    }
    public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.orders');
    }
    public static function getNavigationBadge(): ?string
    {
        return Order::count();
    }
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return static::getModel()::query()->latest();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
            'view' => Pages\view::route('/{record}')

        ];
    }
}
