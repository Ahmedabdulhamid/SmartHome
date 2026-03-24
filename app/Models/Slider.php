<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;
class Slider extends Model
{
    protected $fillable= ['name','path','desc'];
    use HasTranslations;
    protected $translatable=['name','desc'];
    protected $casts=['name'=>"array",'desc'=>"array"];
    protected static function booted()
    {
        static::deleting(function ($slider) {
            // Delete the associated file from storage
            if ($slider->path && Storage::disk('public')->exists($slider->path)) {
                Storage::disk('public')->delete($slider->path);
            }
        });
        static::updated(function ($slider) {
            $original = $slider->getOriginal();

            // path
            if (isset($original['path']) && $original['path'] !== $slider->path) {
                if ($original['path'] && Storage::disk('public')->exists($original['path'])) {
                    Storage::disk('public')->delete($original['path']);
                }
            }
        });
    }
}
