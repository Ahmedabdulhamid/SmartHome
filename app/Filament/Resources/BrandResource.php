<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Filament\Resources\BrandResource\RelationManagers\DistributorsRelationManager;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
     protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Translations')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('English')
                            ->schema([
                                Forms\Components\TextInput::make('name.en')
                                    ->label('Name (English)')
                                    ->required()
                                    ->unique(table:'brands', column: 'name->en',ignoreRecord: true)

                                    ->maxLength(255),
                            ]),
                        Forms\Components\Tabs\Tab::make('العربية')
                            ->schema([
                                Forms\Components\TextInput::make('name.ar')
                                    ->label('الاسم (بالعربية)')
                                    ->required()
                                   ->unique(table:'brands', column: 'name->ar',ignoreRecord: true)

                                    ->maxLength(255),
                            ]),
                    ])
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('logo')
                    ->label(__('filament::admin.logo'))
                    ->image()
                    ->disk('public')
                    ->directory('brands')
                     // لو عايز تغير الاسم
                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file) {
                        $newName = 'brand_' . time() . '.' . $file->getClientOriginalExtension();
                        return $file->storeAs('brands', $newName, 'public');
                    })->deleteUploadedFileUsing(function (string $file) {
                        // $file هو المسار النسبي للملف المخزن (مثل: brands/اسم_الملف.png)
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                    })
                    ->required()->columnSpanFull(),



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
    ->label(__('filament::admin.logo'))
    ->disk('public') // 💡 أضف هذه لربط العمود بالـ disk الصحيح
    ->width(80)
    ->height(80)
    ->rounded(),

// وإذا كنت تريد الاحتفاظ بـ getStateUsing، يجب أن تتحقق:
 Tables\Columns\ImageColumn::make('logo')
     ->label(__('filament::admin.logo'))
    ->getStateUsing(function ($record) {
        return $record->logo ? url('storage/' . $record->logo) : null;
   })
     ->width(80)->height(80)->rounded(),
                Tables\Columns\TextColumn::make('name')->label(__('filament::admin.name'))->getStateUsing(fn($record) => $record->getTranslation('name', app()->getLocale()))->searchable()->sortable(),



                Tables\Columns\TextColumn::make('created_at')
                 ->label(__('filament::admin.created_at'))
                    ->dateTime()
                    ->label('Created At'),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->using(function (Brand $record) {
                        if ($record->logo) {
                            \Storage::disk('public')->delete($record->logo);
                        }
                        $record->delete();
                    }),
                Tables\Actions\ViewAction::make(),
            ])
           ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // 💡 التعديل هنا لحذف ملفات الشعار قبل حذف السجلات بالجملة
                    Tables\Actions\DeleteBulkAction::make()
                        ->using(function (\Illuminate\Support\Collection $records) {
                            foreach ($records as $record) {
                                if ($record->logo) {
                                    \Storage::disk('public')->delete($record->logo);
                                }
                                $record->delete();
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //DistributorsRelationManager::class
        ];
    }
      public static function getNavigationGroup(): ?string
    {
        return __('filament::admin.product_management');
    }
    public static function getPluralLabel(): ?string
    {
        return __('filament::admin.brands'); // المسؤولين
    }

    public static function getLabel(): ?string
    {
        return __('filament::admin.brands'); // مسؤول
    }
     public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record:slug}/edit'),
        ];
    }
}
