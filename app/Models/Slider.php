<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
class Slider extends Model
{
    protected $fillable= ['name','path','desc'];
    use HasTranslations;
    protected $translatable=['name','desc'];
    protected $casts=['name'=>"array",'desc'=>"array"];
}
