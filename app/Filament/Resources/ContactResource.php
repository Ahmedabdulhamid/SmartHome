<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Mail\ContactReplyMail;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Mail;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label(__('filament::admin.name'))
                ->searchable(),
                TextColumn::make('email')
                ->label(__('filament::admin.email'))
                ->searchable()
                ->toggleable(isToggledHiddenByDefault:true),
                TextColumn::make('subject')
                ->label(__('filament::admin.subject'))
                ->searchable()
                ->limit(20),
                 TextColumn::make('message')
                ->label(__('filament::admin.message'))
                ->limit(20)
                ->searchable()
                ->toggleable(isToggledHiddenByDefault:true)



            ])
            ->filters([
                SelectFilter::make('status')
                ->label(__('filament::admin.status'))
                ->options([
                    'new'=>__('filament::admin.new'),
                    "read"=>__('filament::admin.read'),
                    "responded"=>__('filament::admin.responded')
                ])
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make(__('filament::admin.reply'))
                ->label(__('filament::admin.reply'))
                ->icon('heroicon-o-paper-airplane')
                ->form([
                    Forms\Components\Textarea::make('message')
                    ->label(__('filament::admin.message'))
                    ->required()
                ])
                ->action(function($record,array $data){
                    Mail::to($record->email)
                    ->send(new ContactReplyMail($data['message']));
                    $record->update([
                        'status'=>"responded"
                    ]);
                    Notification::make()
                    ->title(__('filament::admin.success_send_reply'))
                    ->success()
                    ->send();
                }),
                Tables\Actions\Action::make(__('filament::admin.mark_as_read'))
                ->label(__('filament::admin.mark_as_read'))

                ->color('success')
                ->action(function($record,array $data){

                   $record->update([
                    'status'=>"read"
                   ]);
                   Notification::make()
                   ->title(__('filament::admin.status_updatad_successfully'))
                   ->success()
                   ->send();
                })    ->disabled(fn($record) => $record->status !== 'new'),


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
    return $infolist
        ->schema([
            Section::make(__('filament::admin.contact_details'))
                ->schema([
                    Grid::make(2) // عمودين للترتيب
                        ->schema([
                            TextEntry::make('name')
                                ->label(__('filament::admin.name'))
                                ->size('lg')
                                ->weight('bold'),

                            TextEntry::make('email')
                                ->label(__('filament::admin.email'))
                                ->copyable() // تقدر تعمل Copy
                                ->icon('heroicon-o-envelope')
                                ->color('primary'),

                            TextEntry::make('subject')
                                ->label(__('filament::admin.subject'))
                                ->columnSpanFull()
                                ->weight('medium')
                                ->icon('heroicon-o-bookmark'),

                            TextEntry::make('status')
                                ->label(__('filament::admin.status'))
                                ->badge()
                                ->colors([
                                    'warning' => 'new',
                                    'success' => 'responded',
                                    'gray' => 'read',
                                ])
                                ->columnSpanFull(),
                        ]),

                    Section::make(__('filament::admin.message'))
                        ->schema([
                            TextEntry::make('message')
                                ->markdown() // عشان الرسالة تبقى مقروءة كويس
                                ->columnSpanFull(),
                        ])
                        ->collapsible(), // الرسالة قابلة للطي/الفتح
                ]),
        ]);
}
   public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.contact_management');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.contacts'); // المسؤولين
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.contacts'); // مسؤول
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
