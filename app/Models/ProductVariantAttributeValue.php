<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantAttributeValue extends Model
{
    protected $table = 'product_variant_attribute_value';

    protected $fillable = [
        'product_variant_id',
        'attribute_value_id',
        'attribute_id'
    ];
    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
