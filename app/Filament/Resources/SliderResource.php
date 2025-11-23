<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Resources\SliderResource\RelationManagers;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Components\Textarea;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make(__('filament::admin.translation'))
                    ->tabs([
                        Tab::make(__('filament::admin.english'))
                            ->schema([
                                TextInput::make('name.en')
                                    ->label(__('filament::admin.name_english'))
                                    ->required()

                            ]),
                        Tab::make(__('filament::admin.arabic'))
                            ->schema([
                                TextInput::make('name.ar')
                                    ->label(__('filament::admin.name_arabic'))
                                    ->required()

                            ])
                    ])
                    ->columnSpanFull(),

                Tabs::make(__('filament::admin.translation'))
                    ->tabs([
                        Tab::make(__('filament::admin.english'))
                            ->schema([
                                Textarea::make('desc.en')
                                    ->label(__('filament::admin.desc_en'))
                                    ->required()
                                    ->rows(10)
                                    ->cols(20)

                            ]),
                        Tab::make(__('filament::admin.arabic'))
                            ->schema([
                                Textarea::make('desc.ar')
                                    ->label(__('filament::admin.desc_ar'))
                                    ->required()
                                    ->rows(10)
                                    ->cols(20)

                            ])
                    ])
                    ->columnSpanFull(),

                FileUpload::make('path')
                    ->label(__('filament::admin.upload_image'))
                    ->image()
                    ->disk('public')
                    ->directory('sliders')
                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                        $newName = 'slider_' . time() . '.' . $file->getClientOriginalExtension();
                        return $file->storeAs('sliders', $newName, 'public');
                    })
                    ->required()->columnSpanFull(),





            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path')
                    ->label(__('filament::admin.image_slider'))->getStateUsing(fn($record) => asset('storage/' . $record->path))->width(80),
                TextColumn::make('name')
                    ->label(__('filament::admin.name'))
                    ->getStateUsing(fn($record) => $record->getTranslation('name', app()->getLocale())),
                TextColumn::make('desc')
                    ->label(__('filament::admin.desc'))
                    ->getStateUsing(fn($record) => $record->getTranslation('desc', app()->getLocale()))
                    ->limit(20)

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
        return __('filament::admin.slider_management');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.sliders'); // المسؤولين
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.sliders'); // مسؤول
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
