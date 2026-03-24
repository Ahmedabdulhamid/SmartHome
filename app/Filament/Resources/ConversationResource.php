<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConversationResource\Pages;
use App\Filament\Resources\ConversationResource\RelationManagers;
use App\Models\Conversation;
use App\Models\WhatsappSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class ConversationResource extends Resource
{
    protected static ?string $model = Conversation::class;

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
                Tables\Columns\TextColumn::make('external_number')
                    ->label('رقم العميل')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'open' => 'success',
                        'closed' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label('الموظف المسؤول'),

                Tables\Columns\TextColumn::make('unread_count')
                    ->label('غير مقروء')
                    ->badge()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('last_message_at')
                    ->label('آخر ظهور')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'مفتوحة',
                        'closed' => 'مغلقة',
                    ]),
            ])
            ->filters([
                //
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
        return $infolist
            ->schema([
                Section::make('تفاصيل المحادثة')
                    ->schema([
                        TextEntry::make('external_number')->label('العميل:'),
                        TextEntry::make('status')
                            ->label('الحالة:')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'open' => 'success',
                                'closed' => 'danger',
                                default => 'gray',
                            }),
                    ])
                    // الحل هنا: نضع الـ Action في الـ headerActions التابع للـ Section
                    ->headerActions([
                        Action::make('Send')
                            ->label('إرسال رد')
                            ->icon('heroicon-m-paper-airplane')
                            ->form([
                                TextInput::make('reply')
                                    ->label('نص الرسالة')
                                    ->required(),
                            ])
                            ->action(function (Conversation $record, array $data) {
                                // 1. جلب الإعدادات
                                $whatsappSetting = \App\Models\WhatsappSetting::first();

                                if (!$whatsappSetting || empty($whatsappSetting->phone_number_id) || empty($whatsappSetting->meta_access_token)) {
                                    \Filament\Notifications\Notification::make()
                                        ->title('خطأ في الإعدادات')
                                        ->body('بيانات Meta API غير مكتملة في جدول الإعدادات.')
                                        ->danger()
                                        ->send();
                                    return; // توقف عن التنفيذ
                                }

                                $phoneId = $whatsappSetting->phone_number_id;
                                $accessToken = trim($whatsappSetting->meta_access_token);
                                // تنظيف رقم الهاتف (إزالة أي إضافات مثل whatsapp:)
                                $recipientNumber = str_replace(['whatsapp:', '+', ' '], '', $record->external_number);

                                try {
                                    // 2. الإرسال إلى API واتساب
                                    $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
                                        ->post("https://graph.facebook.com/v21.0/{$phoneId}/messages", [
                                            'messaging_product' => 'whatsapp',
                                            'recipient_type' => 'individual',
                                            'to' => $recipientNumber,
                                            'type' => 'text',
                                            'text' => [
                                                'preview_url' => false,
                                                'body' => $data['reply'],
                                            ],
                                        ]);

                                    if ($response->successful()) {
                                        // 3. حفظ الرسالة في قاعدة البيانات عند نجاح الإرسال
                                        $record->messages()->create([
                                            'body' => $data['reply'],
                                            'direction' => 'outbound',
                                            'admin_id' => auth()->guard('admin')->id(),
                                            'external_number' => $record->external_number,
                                            'type' => 'text',
                                            'status' => 'sent', // أو 'delivered' بناءً على استجابة الـ Webhook لاحقاً
                                        ]);

                                        $record->update(['last_message_at' => now()]);

                                        \Filament\Notifications\Notification::make()
                                            ->title('تم الإرسال')
                                            ->success()
                                            ->send();
                                    } else {
                                        // معالجة خطأ API ميتـا
                                        $errorMessage = $response->json()['error']['message'] ?? 'فشل الإرسال عبر ميتـا';
                                        logger()->error('Meta API Error: ' . $response->body());

                                        \Filament\Notifications\Notification::make()
                                            ->title('فشل الإرسال')
                                            ->body($errorMessage)
                                            ->danger()
                                            ->send();
                                    }
                                } catch (\Exception $e) {
                                    logger()->error('WhatsApp Send Exception: ' . $e->getMessage());
                                    \Filament\Notifications\Notification::make()
                                        ->title('خطأ تقني')
                                        ->body('حدث خطأ أثناء محاولة الاتصال بـ Meta API.')
                                        ->danger()
                                        ->send();
                                }
                            })
                    ])
                    ->columns(2),

                Section::make('سجل الرسائل')
                    ->schema([
                        ViewEntry::make('messages')
                            ->label('')
                            ->view('filament.resources.conversations.pages.chat-history')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConversations::route('/'),
            'create' => Pages\CreateConversation::route('/create'),
            'edit' => Pages\EditConversation::route('/{record}/edit'),
            'view' => Pages\ViewChatMessage::route('/{record}')
        ];
    }
}
