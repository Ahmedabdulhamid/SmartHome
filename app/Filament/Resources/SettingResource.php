<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\TextColumn;
use Spatie\LaravelSettings\Settings;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                // 1. البيانات الأساسية (Basic Data)
                //
                Forms\Components\Section::make(__('filament::admin.basic_data'))
                    ->description(__('filament::admin.basic_data_desc'))
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('site_address.en')->label(__('filament::admin.site_address_en'))->required(),
                                Forms\Components\TextInput::make('site_address.ar')->label(__('filament::admin.site_address_ar'))->required(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('site_name.en')->label(__('filament::admin.site_name_en'))->required(),
                                Forms\Components\TextInput::make('site_name.ar')->label(__('filament::admin.site_name_ar'))->required(),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('site_email')->label(__('filament::admin.site_email'))->required()->email(),
                                Forms\Components\TextInput::make('site_phone')
                                    ->label(__('filament::admin.site_phone'))
                                    ->tel() // input type="tel"
                                    ->required()
                                    ->rules([
                                        'regex:/^(\+?[0-9]{8,15})$/'
                                    ])
                                    ->helperText('أدخل رقم هاتف صحيح (8 - 15 رقم، ويمكن أن يبدأ بـ +)')

                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\FileUpload::make('site_logo')
                                    ->label(__('filament::admin.site_logo'))
                                    ->image()->disk('public')->directory('settings')
                                    ->required()
                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                                        $newName = 'setting_' . time() . '.' . $file->getClientOriginalExtension();
                                        return $file->storeAs('settings', $newName, 'public');
                                    }),

                                Forms\Components\FileUpload::make('favicon')
                                    ->label(__('filament::admin.favicon'))
                                    ->image()->disk('public')->directory('settings')
                                    ->required()
                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                                        $newName = 'favicon_' . time() . '.' . $file->getClientOriginalExtension();
                                        return $file->storeAs('favicons', $newName, 'public');
                                    })

                            ]),
                    ])->columns(1), // نستخدم columns(1) لأننا استخدمنا Grid بالداخل

                //
                // 2. روابط السوشيال ميديا (Social Media)
                //
                Forms\Components\Section::make(__('filament::admin.social_media'))
                    ->schema([
                        Forms\Components\TextInput::make('facebook_url')->label(__('filament::admin.facebook_url'))->url(),
                        Forms\Components\TextInput::make('twitter_url')->label(__('filament::admin.twitter_url'))->url(),
                        Forms\Components\TextInput::make('instagram_url')->label(__('filament::admin.instagram_url'))->url(),
                        Forms\Components\TextInput::make('linkedin_url')->label(__('filament::admin.linkedin_url'))->url(),
                        Forms\Components\TextInput::make('youtube_url')->label(__('filament::admin.youtube_url'))->url(),
                    ])->columns(2),

                //
                // 3. إعدادات SEO
                //
                Forms\Components\Section::make(__('filament::admin.seo_settings'))
                    ->schema([
                        // Meta Title
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('meta_title.en')->label(__('filament::admin.meta_title_en')),
                            Forms\Components\TextInput::make('meta_title.ar')->label(__('filament::admin.meta_title_ar')),
                        ]),

                        // Meta Description
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\RichEditor::make('meta_description.en')->label(__('filament::admin.meta_description_en')),
                            Forms\Components\RichEditor::make('meta_description.ar')->label(__('filament::admin.meta_description_ar')),
                        ]),

                        // Meta Keywords
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\RichEditor::make('meta_keywords.en')->label(__('filament::admin.meta_keywords_en')),
                            Forms\Components\RichEditor::make('meta_keywords.ar')->label(__('filament::admin.meta_keywords_ar')),
                        ]),

                        // OG (Open Graph) Settings
                        Forms\Components\Section::make(__('filament::admin.og_settings'))
                            ->schema([
                                Forms\Components\FileUpload::make('og_image')
                                    ->label(__('filament::admin.og_image'))->image()->disk('public')->directory('settings'),
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('og_title.en')->label(__('filament::admin.og_title_en')),
                                    Forms\Components\TextInput::make('og_title.ar')->label(__('filament::admin.og_title_ar')),
                                ]),
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\RichEditor::make('og_description.en')->label(__('filament::admin.og_description_en')),
                                    Forms\Components\RichEditor::make('og_description.ar')->label(__('filament::admin.og_description_ar')),
                                ]),
                            ])->columns(1),

                        // Twitter Card Settings
                        Forms\Components\Section::make(__('filament::admin.twitter_card_settings'))
                            ->schema([
                                Forms\Components\FileUpload::make('twitter_card_image')
                                    ->label(__('filament::admin.twitter_card_image'))->image()->disk('public')->directory('settings'),
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('twitter_card_title.en')->label(__('filament::admin.twitter_card_title_en')),
                                    Forms\Components\TextInput::make('twitter_card_title.ar')->label(__('filament::admin.twitter_card_title_ar')),
                                ]),
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\RichEditor::make('twitter_card_description.en')->label(__('filament::admin.twitter_card_description_en')),
                                    Forms\Components\RichEditor::make('twitter_card_description.ar')->label(__('filament::admin.twitter_card_description_ar')),
                                ]),
                            ])->columns(1),
                    ])->columns(1), // نستخدم columns(1) لأن الأقسام الداخلية تستخدم Grid
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. اسم الموقع (متعدد اللغات)
                Tables\Columns\TextColumn::make('site_name')
                    ->label(__('filament::admin.site_name'))
                    // 🔥 عرض قيمة الموقع بناءً على اللغة الحالية
                    // (يجب أن يكون الحقل محفوظاً كـ JSON)
                    ->getStateUsing(fn(Setting $record): string => $record->getTranslation('site_name', app()->getLocale()))
                    ->searchable()
                    ->sortable(),

                // 2. البريد الإلكتروني
                Tables\Columns\TextColumn::make('site_email')
                    ->label(__('filament::admin.site_email'))
                    ->searchable()
                    ->sortable(),

                // 3. الهاتف
                Tables\Columns\TextColumn::make('site_phone')
                    ->label(__('filament::admin.site_phone'))
                    ->searchable(),

                // 4. عرض صورة الشعار (إذا كنت تريدها كصورة بدلاً من رابط)
                Tables\Columns\ImageColumn::make('site_logo')
                    ->label(__('filament::admin.site_logo'))
                    ->disk('public'), // تأكد من أن الصور محفوظة في هذا القرص

                // 5. رابط الفيسبوك
                Tables\Columns\TextColumn::make('facebook_url')
                    ->label(__('filament::admin.facebook_url'))
                    ->url(fn(Setting $record): ?string => $record->facebook_url)
                    ->openUrlInNewTab()
                    ->limit(30),

                // 6. آخر تحديث
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament::admin.last_updated'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // يمكنك إضافة ViewAction هنا
            ])
            ->bulkActions([
                // إذا كان هناك صفوف متعددة
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
        return __('filament::admin.settings_management');
    }

    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.settings'); // عروض الأسعار
    }
    public static function getLabel(): ?string
    {
        return __('filament::admin.settings'); // عرض سعر
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
