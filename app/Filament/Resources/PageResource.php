<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\Tabs;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Tabs::make('Title Tabs')
                    ->tabs([
                        Tabs\Tab::make('ar')
                            ->label('العنوان (العربية)')
                            ->schema([
                                Forms\Components\TextInput::make('title.ar')
                                    ->label(__('filament::admin.title'))
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->rule('unique:pages,title->ar')
                                    // توليد الـ Slug من العنوان العربي
                                    ->live(onBlur: true)

                            ]),
                        Tabs\Tab::make('en')
                            ->label('Title (English)')
                            ->schema([
                                Forms\Components\TextInput::make('title.en')
                                    ->label(__('filament::admin.title'))
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->rule('unique:pages,title->en')
                                    ->maxLength(255),
                            ]),
                    ])
                    ->columnSpanFull(),

                // 💡 تقسيم المحتوى إلى ألسنة للترجمة
                Tabs::make('Content Tabs')
                    ->tabs([
                        // المحتوى العربي
                        Tabs\Tab::make('ar')
                            ->label('المحتوى (العربية)')
                            ->schema([
                                TiptapEditor::make('content.ar')
                                    ->label(__('filament::admin.content'))
                                    ->required()
                                    ->disk('public')
                                    ->directory('pages-content')

                            ]),

                        // المحتوى الإنجليزي
                        Tabs\Tab::make('en')
                            ->label('Content (English)')
                            ->schema([
                                TiptapEditor::make('content.en')
                                    ->label(__('filament::admin.content'))
                                    ->required()

                                    ->disk('public')
                                    ->directory('pages-content'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament::admin.title'))
                    ->searchable()
                    ->getStateUsing(fn($record) => $record->getTranslation('title', app()->getLocale()))
                    ->sortable(),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
    public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.pages_management');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.pages'); // المسؤولين
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.pages'); // مسؤول
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
