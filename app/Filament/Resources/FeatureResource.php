<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeatureResource\Pages;
use App\Models\Feature;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class FeatureResource extends Resource
{
    protected static ?string $model = Feature::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';




    public static function getNavigationLabel(): string
    {
        return __('filament::admin.features_navigation_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('filament::admin.feature_info_section'))
                    ->columns(3)
                    ->schema([
                        Tabs::make('Translations')
                            ->columnSpanFull()
                            ->tabs([
                                Tab::make(__('filament::admin.arabic_tab_title'))
                                    ->schema([
                                        TextInput::make('name.ar')
                                            ->label(__('filament::admin.feature_name_ar'))
                                            ->required()
                                            ->maxLength(255)
                                            ->rule(fn($record) => Rule::unique('features', 'name->ar')->ignore($record)),

                                        MarkdownEditor::make('description.ar')
                                            ->label(__('filament::admin.feature_description_ar'))
                                            ->maxLength(1000)
                                            ->nullable(),
                                    ]),

                                Tab::make(__('filament::admin.english_tab_title'))
                                    ->schema([
                                        TextInput::make('name.en')
                                            ->label(__('filament::admin.feature_name_en'))
                                            ->required()
                                            ->maxLength(255)
                                            ->rule(fn($record) => Rule::unique('features', 'name->en')->ignore($record)),

                                        MarkdownEditor::make('description.en')
                                            ->label(__('filament::admin.feature_description_en'))
                                            ->maxLength(1000)
                                            ->nullable(),
                                    ]),
                            ]),

                        FileUpload::make('icon')
                            ->label(__('filament::admin.feature_icon'))
                            ->helperText(__('filament::admin.feature_icon_helper'))
                            ->image()
                            ->acceptedFileTypes(['image/svg+xml', 'image/png', 'image/jpeg'])
                            ->directory('feature-icons')
                            ->columnSpan(2)
                            ->maxSize(512)
                            ->nullable(),

                        Toggle::make('is_active')
                            ->label(__('filament::admin.is_active_label'))
                            ->default(true)
                            ->columnSpan(1)
                            ->helperText(__('filament::admin.is_active_helper')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament::admin.feature_name'))
                    ->sortable()
                    ->searchable(),

                ImageColumn::make('icon')
                    ->label(__('filament::admin.feature_icon'))
                    ->square()
                    ->size(40),

                TextColumn::make('description')
                    ->label(__('filament::admin.feature_description'))
                    ->limit(50)
                    ->tooltip(fn(Feature $record): string => $record->description)
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label(__('filament::admin.is_active_label'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('filament::admin.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('filament::admin.no_features_yet'))
            ->emptyStateDescription(__('filament::admin.add_new_feature_btn'));
    }

    public static function getRelations(): array
    {
        return [];
    }
public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.services_management');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }
       public static function getPluralLabel(): ?string
    {
        return __('filament::admin.features'); // المسؤولين
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeatures::route('/'),
            'create' => Pages\CreateFeature::route('/create'),
            'edit' => Pages\EditFeature::route('/{record}/edit'),
        ];
    }
}
