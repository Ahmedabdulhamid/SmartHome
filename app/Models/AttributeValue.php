<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class AttributeValue extends Model
{
    protected $guarded=['id'];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }
    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'product_variant_attribute_value'
        );
    }
    public function productVariants()
{
    return $this->belongsToMany(ProductVariant::class, 'product_variant_attribute_value');
}
}
