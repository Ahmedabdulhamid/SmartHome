<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make(__('filament::admin.translations'))
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('filament::admin.arabic'))
                            ->schema([
                                Forms\Components\TextInput::make('question.ar')
                                    ->label(__('filament::admin.question_ar'))
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('answer.ar')
                                    ->label(__('filament::admin.answer_ar'))
                                    ->required()
                                    ->rows(4),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('filament::admin.english'))
                            ->schema([
                                Forms\Components\TextInput::make('question.en')
                                    ->label(__('filament::admin.question_en'))
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Textarea::make('answer.en')
                                    ->label(__('filament::admin.answer_en'))
                                    ->required()
                                    ->rows(4),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('filament::admin.edit')),

                Tables\Actions\DeleteAction::make()
                    ->label(__('filament::admin.delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('filament::admin.delete_selected')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return  __('filament::admin.content');

    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.faqs');

    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.faq');
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
