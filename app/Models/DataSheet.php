<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
class DataSheet extends Model
{
    protected $fillable = ['product_id', 'file_path', 'name'];
     use HasTranslations;
    public $translatable = ['name'];
    protected $casts = [
        'name' => 'array', // لتحويل JSON إلى مصفوفة تلقائيًا
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
