<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\DateTimePicker;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry; // سنستخدم TextEntry مع html() بدلاً من HtmlEntry
use Filament\Infolists\Components\Tabs as InfolistTabs;
use Filament\Infolists\Components\Tabs\Tab as InfolistTab; // تم التعديل هنا
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\SpatieTranslatableTextEntry;
use Illuminate\Support\Str;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make(__('filament::admin.blog_content'))
                    ->tabs([

                        // --- المحتوى الأساسي ---
                        Tab::make(__('filament::admin.main_content'))
                            ->icon('heroicon-o-pencil')
                            ->schema([

                                Tabs::make(__('filament::admin.translations'))
                                    ->tabs([
                                        Tab::make(__('filament::admin.english'))
                                            ->schema([
                                                TextInput::make('title.en')
                                                    ->label(__('filament::admin.title_en'))
                                                    ->required()
                                                    ->maxLength(255),


                                                RichEditor::make('excerpt.en')
                                                    ->label(__('filament::admin.excerpt_en')),

                                                TiptapEditor::make('content.en')
                                                    ->label(__('filament::admin.content_en'))
                                                    ->disk('public')
                                                    ->directory('blogs-content')
                                                    ->required()
                                                    ->columnSpanFull(),
                                            ]),

                                        Tab::make(__('filament::admin.arabic'))
                                            ->schema([
                                                TextInput::make('title.ar')
                                                    ->label(__('filament::admin.title_ar'))
                                                    ->required()
                                                    ->maxLength(255),

                                                RichEditor::make('excerpt.ar')
                                                    ->label(__('filament::admin.excerpt_ar')),

                                                TiptapEditor::make('content.ar')
                                                    ->label(__('filament::admin.content_ar'))
                                                    ->disk('public')
                                                    ->directory('blogs-content')
                                                    ->required()
                                                    ->columnSpanFull(),
                                            ]),
                                    ])->columnSpanFull(),

                                Section::make(__('filament::admin.basic_data'))
                                    ->columns(2)
                                    ->schema([
                                        Select::make('category_id')
                                            ->label(__('filament::admin.category'))
                                            ->relationship('category', 'name', fn($query) => $query->where('type', 'blogs'))
                                            ->required()
                                            ->searchable()
                                            ->preload(),

                                        Select::make('parent_id')
                                            ->label(__('filament::admin.parent_blog'))
                                            ->relationship('parent', 'title')
                                            ->nullable()
                                            ->searchable()
                                            ->preload()
                                            ->helperText(__('filament::admin.parent_blog_helper')),
                                    ]),
                            ]),

                        // --- الوسائط و SEO ---
                        Tab::make(__('filament::admin.media_seo'))
                            ->icon('heroicon-o-photo')
                            ->schema([

                                Section::make(__('filament::admin.media'))
                                    ->columns(1)
                                    ->schema([
                                        FileUpload::make('featured_image')
                                            ->label(__('filament::admin.featured_image'))
                                            ->image()
                                            ->disk('public')
                                            ->directory('blog-featured-images')
                                            ->nullable()
                                            ->columnSpanFull(),

                                        TextInput::make('video_url')
                                            ->label(__('filament::admin.video_url'))
                                            ->url()
                                            ->nullable()
                                            ->helperText(__('filament::admin.video_url_helper')),
                                    ]),

                                Section::make(__('filament::admin.seo_meta'))
                                    ->columns(1)
                                    ->schema([


                                        Tabs::make(__('filament::admin.meta_translations'))
                                            ->tabs([
                                                Tab::make(__('filament::admin.english'))
                                                    ->schema([
                                                        TextInput::make('meta_description.en')
                                                            ->label(__('filament::admin.meta_description_en'))
                                                            ->maxLength(160)
                                                            ->helperText(__('filament::admin.seo_helper')),
                                                    ]),
                                                Tab::make(__('filament::admin.arabic'))
                                                    ->schema([
                                                        TextInput::make('meta_description.ar')
                                                            ->label(__('filament::admin.meta_description_ar'))
                                                            ->maxLength(160)
                                                            ->helperText(__('filament::admin.seo_helper')),
                                                    ]),
                                            ]),
                                    ]),
                            ]),

                        // --- الحالة والنشر ---
                        Tab::make(__('filament::admin.status_publish'))
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Section::make(__('filament::admin.publish_status_author'))
                                    ->columns(2)
                                    ->schema([
                                        Toggle::make('is_published')
                                            ->label(__('filament::admin.is_published'))
                                            ->default(false)
                                            ->helperText(__('filament::admin.is_published_helper')),

                                        DateTimePicker::make('published_at')
                                            ->label(__('filament::admin.published_at'))
                                            ->nullable()
                                            ->helperText(__('filament::admin.published_at_helper')),

                                        Select::make('author_id')
                                            ->label(__('filament::admin.author'))
                                            ->relationship('author', 'name')
                                            ->required()
                                            ->default(auth()->guard('admin')->check() ? auth()->guard('admin')->id() : null),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        // الرسالة الافتراضية للقيم الفارغة
        $notFoundMessage = __('filament::admin.not_found');

        return $infolist
            ->schema([
                // بيانات المقالة الأساسية
                Fieldset::make(__('filament::admin.basic_info'))
                    ->columns(3)
                    ->schema([
                        TextEntry::make('id')
                            ->label(__('filament::admin.id')),
                        TextEntry::make('author.name')
                            ->label(__('filament::admin.author'))
                            ->placeholder($notFoundMessage),
                        TextEntry::make('category.name')
                            ->label(__('filament::admin.category'))
                            ->placeholder($notFoundMessage),
                        TextEntry::make('parent.title')
                            ->label(__('filament::admin.parent_blog'))
                            ->placeholder($notFoundMessage),
                        TextEntry::make('published_at')
                            ->label(__('filament::admin.published_at'))
                            ->dateTime()
                            ->placeholder($notFoundMessage),
                        TextEntry::make('is_published')
                            ->label(__('filament::admin.is_published'))
                            ->badge()
                            ->formatStateUsing(fn (bool $state) => $state ? __('filament::admin.yes') : __('filament::admin.no'))
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                    ]),
                    Fieldset::make(__('filament::admin.media_info'))
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('featured_image')
                            ->label(__('filament::admin.featured_image'))
                            ->disk('public')
                            ->size(200) // حجم مناسب للعرض
                            ->columnSpan(1)
                            ->placeholder($notFoundMessage),

                        TextEntry::make('video_url')
                            ->label(__('filament::admin.video_url'))
                            ->url(fn ($state) => $state, true) // يجعل الرابط قابلاً للنقر ويفتح في نافذة جديدة
                            ->icon('heroicon-o-play') // تغيير الأيقونة
                            ->badge() // الأهم: لتحويله إلى شكل بادج (مثل الزر)
                            ->formatStateUsing(fn (string $state): string => __('filament::admin.watch_video')) // تغيير النص المعروض
                            ->color('info') // تغيير اللون
                            ->placeholder($notFoundMessage)
                            ->columnSpan(1),
                    ]),


                // المحتوى المترجم
                InfolistTabs::make('Translations')
                    ->tabs([
                        // تبويب المحتوى الإنجليزي
                        InfolistTab::make(__('filament::admin.english'))
                            ->schema([
                                TextEntry::make('title')
                                    ->label(__('filament::admin.title'))
                                    ->getStateUsing(fn (Blog $record): ?string => $record->getTranslation('title', 'en'))
                                    ->placeholder($notFoundMessage),
                                TextEntry::make('excerpt')
                                    ->label(__('filament::admin.excerpt'))
                                    ->getStateUsing(fn (Blog $record): ?string => $record->getTranslation('excerpt', 'en'))
                                    ->html() // لعرض الـ RichEditor Excerpt
                                    ->placeholder($notFoundMessage),

                                // تم التعديل من HtmlEntry إلى TextEntry::make(...)->html()
                                TextEntry::make('content.en')
                                    ->label(__('filament::admin.content'))
                                    ->html()
                                    ->getStateUsing(fn (Blog $record): ?string => $record->getTranslation('content', 'en'))
                                    ->placeholder($notFoundMessage)
                                    ->columnSpanFull(),
                            ]),

                        // تبويب المحتوى العربي
                        InfolistTab::make(__('filament::admin.arabic'))
                            ->schema([
                                TextEntry::make('title')
                                    ->label(__('filament::admin.title'))
                                    ->getStateUsing(fn (Blog $record): ?string => $record->getTranslation('title', 'ar'))
                                    ->placeholder($notFoundMessage),
                                TextEntry::make('excerpt')
                                    ->label(__('filament::admin.excerpt'))
                                    ->getStateUsing(fn (Blog $record): ?string => $record->getTranslation('excerpt', 'ar'))
                                    ->html()
                                    ->placeholder($notFoundMessage),

                                // تم التعديل من HtmlEntry إلى TextEntry::make(...)->html()
                                TextEntry::make('content.ar')
                                    ->label(__('filament::admin.content'))
                                    ->html()
                                    ->getStateUsing(fn (Blog $record): ?string => $record->getTranslation('content', 'ar'))
                                    ->placeholder($notFoundMessage)
                                    ->columnSpanFull(),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([


                TextColumn::make('title')
                    ->label(__('filament::admin.title'))
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                TextColumn::make('category.name')
                    ->label(__('filament::admin.category'))
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_published')
                    ->label(__('filament::admin.is_published'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('author.name')
                    ->label(__('filament::admin.author'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('views_count')
                    ->label(__('filament::admin.views'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label(__('filament::admin.published_at'))
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label(__('filament::admin.updated_at'))
                    ->toggleable(isToggledHiddenByDefault:true)
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label(__('filament::admin.publish_status'))
                    ->trueLabel(__('filament::admin.published'))
                    ->falseLabel(__('filament::admin.unpublished'))
                    ->placeholder(__('filament::admin.all')),

                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->label(__('filament::admin.category_filter')),

                Tables\Filters\Filter::make('published_from')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')
                            ->label(__('filament::admin.published_from')),
                        Forms\Components\DatePicker::make('published_until')
                            ->label(__('filament::admin.published_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(__('filament::admin.view')),
                Tables\Actions\EditAction::make()->label(__('filament::admin.edit')),
                Tables\Actions\DeleteAction::make()->label(__('filament::admin.delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('filament::admin.delete')),
                ]),
            ]);
    }

    public static function getPluralLabel(): string
    {
        return __('filament::admin.blogs');
    }

    public static function getLabel(): string
    {
        return __('filament::admin.blog');
    }
public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.blogs_management');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
            // تعديل مسار 'view' ليعمل مع Infolist
            'view' => Pages\ViewBlog::route('/{record:id}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug', 'author.name'];
    }
}
