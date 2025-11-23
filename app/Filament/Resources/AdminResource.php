<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Filament\Resources\AdminResource\RelationManagers;
use App\Models\Admin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 1;
    protected static ?int $navigationGroupSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament::admin.user_info'))
                    // Section للبيانات الأساسية
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament::admin.name'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->label(__('filament::admin.email')),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->label(__('filament::admin.password'))->dehydrateStateUsing(fn($state) => Hash::make($state)) // 🔑 تشفير قبل الحفظ
                            ->dehydrated(fn($state) => filled($state)),
                    ])
                    ->columns(2), // يعرض الحقول على عمودين

                Forms\Components\Section::make(__('filament::admin.roles_permissions')) // Section للصلاحيات
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->multiple() // لو ممكن للمستخدم أكثر من Role

                            ->label(__('filament::admin.roles')),

                        Forms\Components\Select::make('permissions')

                            ->relationship('permissions', 'name')
                            ->preload()
                            ->searchable()
                            ->multiple()

                            ->label(__('filament::admin.permissions')),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament::admin.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament::admin.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('filament::admin.roles'))->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('permissions.name')
                    ->label(__('filament::admin.permissions'))->getStateUsing(function (Admin $record) {
                        // هنا نخلي بس أول 2 permissions تظهر
                        $permissions = $record->permissions->pluck('name')->take(2);
                        // لو فيه أكتر من 2، نضيف ...
                        if ($record->permissions->count() > 2) {
                            $permissions->push('...');
                        }
                        return $permissions->join(', ');
                    })->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament::admin.created_at'))->toggleable(isToggledHiddenByDefault: true)
                    ->dateTime(),
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
    public static function getNavigationLabel(): string
    {
        return __('filament::admin.admins'); // ترجع النص من ملف الترجمة
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.admin_management');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.admins'); // المسؤولين
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.admins'); // مسؤول
    }
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(), // زر الإضافة
            $this->getCancelFormAction(), // زر الإلغاء
            // شيلنا createAnother
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
    // 🌟 التعديل المقترح: أضف هذه الدالة إلى AdminResource
public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()->with(['roles', 'permissions']);
}
}

