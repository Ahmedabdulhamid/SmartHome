<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Attribute extends Model
{
    protected $fillable = ['name'];
    use HasTranslations;
    protected $translatable=['name'];
    protected $casts = [
        'name' => 'array',

    ];

public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    // كل Attribute مرتبط بعدة Products
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_attributes');
    }
}
