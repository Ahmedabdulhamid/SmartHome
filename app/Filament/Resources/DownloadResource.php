<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DownloadResource\Pages;
use App\Filament\Resources\DownloadResource\RelationManagers;
use App\Models\Download;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DownloadResource extends Resource
{
    protected static ?string $model = Download::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Translations')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('English')
                            ->schema([
                                Forms\Components\TextInput::make('title.en')
                                    ->label('Title (English)')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Tabs\Tab::make('العربية')
                            ->schema([
                                Forms\Components\TextInput::make('title.ar')
                                    ->label('العنوان (بالعربية)')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ])
                    ->columnSpanFull(),
                Forms\Components\Select::make('type')
                    ->label(__('filament::admin.type'))
                    ->options([
                        'profile' => 'Profile',
                        'manual' => 'User Manual',
                        'catalog' => 'Catalog',
                    ])

                    ->columnSpanFull()
                    ->required(),
                Forms\Components\FileUpload::make('file_path')
                    ->label(__('filament::admin.upload_file'))
                    ->disk('public')
                    ->directory('downloads')
                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                        $newName = 'download_' . time() . '.' . $file->getClientOriginalExtension();
                        return $file->storeAs('downloads', $newName, 'public');
                    })
                    ->required()
                    ->preserveFilenames(false)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label(__('filament::admin.title'))->searchable(),
                Tables\Columns\TextColumn::make('type')->label(__('filament::admin.type'))->sortable(),

                Tables\Columns\TextColumn::make('created_at')->label(__('filament::admin.created_at'))->dateTime(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view')
                    ->label(__('filament::admin.view_pdf'))
                    ->icon('heroicon-o-document-text')
                    ->url(fn($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make() // يفتح الملف في تبويب جديد
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
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.downloads'); // المسؤولين
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.downloads'); // مسؤول
    }
     public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDownloads::route('/'),
            'create' => Pages\CreateDownload::route('/create'),
            'edit' => Pages\EditDownload::route('/{record:slug}/edit'),
        ];
    }
}
