<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateResource\Pages;
use App\Filament\Resources\TemplateResource\RelationManagers;
use App\Models\Template;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\HtmlEntry;
use Filament\Infolists\Components\ColorEntry;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Support\Enums\MaxWidth;
use FilamentTiptapEditor\TiptapEditor;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([


                Forms\Components\FileUpload::make('logo')
                    ->label(__('Logo / الشعار'))
                    ->image()
                    ->directory('templates/logos')
                    ->required()
                    ->preserveFilenames()
                    ->imagePreviewHeight('100')
                    ->downloadable()
                    ->openable(),

                Forms\Components\ColorPicker::make('color_scheme')
                    ->label(__('Primary Color / اللون الأساسي'))
                    ->default('#333333'),

                // ✅ Tabs للغات
                Forms\Components\Tabs::make('Translations')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('English')
                            ->schema([
                                Forms\Components\TextInput::make('name.en')
                                    ->label('Template Name (EN)')
                                    ->required(),

                                Forms\Components\Textarea::make('description.en')
                                    ->label('Description (EN)'),

                                TiptapEditor::make('header_html.en')
                                    ->label('Header (EN)')
                                    ->columnSpanFull(),

                                TiptapEditor::make('body_html.en')
                                    ->label('Body (EN)')
                                    ->columnSpanFull(),



                                TiptapEditor::make('footer_html.en')
                                    ->label('Footer (EN)')
                                    ->columnSpanFull()

                            ]),

                        Forms\Components\Tabs\Tab::make('العربية')
                            ->schema([
                                Forms\Components\TextInput::make('name.ar')
                                    ->label('اسم القالب (AR)')
                                    ->required(),

                                Forms\Components\Textarea::make('description.ar')
                                    ->label('الوصف (AR)'),

                                TiptapEditor::make('header_html.ar')
                                    ->label('الترويسة (AR)')
                                    ->columnSpanFull(),

                                TiptapEditor::make('body_html.ar')
                                    ->label('المحتوى (AR)')
                                    ->columnSpanFull(),

                                TiptapEditor::make('footer_html.ar')
                                    ->label('التذييل (AR)')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament::admin.template_name'))
                    ->getStateUsing(fn($record) => $record->getTranslation('name', app()->getLocale())),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                //Tables\Actions\ViewAction::make(),
                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Template Preview')
                    ->modalWidth(MaxWidth::SevenExtraLarge)
                    ->modalContent(function ($record) {
                        $locale = app()->getLocale();
                        return view('filament.templates.preview', [
                            'template' => $record,
                            'locale' => $locale,
                        ]);
                    }),

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
        return __('filament::admin.template_management');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.templates'); // القوالب
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.templates'); // قالب
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'edit' => Pages\EditTemplate::route('/{record}/edit'),
        ];
    }
}
