<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProductBundle extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function sourceProduct() {
        return $this->belongsTo(Product::class, 'source_product_id');
    }

    public function sourceVariant() {
        return $this->belongsTo(ProductVariant::class, 'source_variant_id');
    }

    public function baseUnit()
    {
        return $this->belongsTo(ProductBaseUnit::class, 'unit_id');
    }

    public function additionalUnit()
    {
        return $this->belongsTo(ProductAdditionalUnit::class, 'unit_id');
    }

    public function unit()
    {
        return $this->unit_type == 0
            ? $this->belongsTo(ProductBaseUnit::class, 'unit_id')
            : $this->belongsTo(ProductAdditionalUnit::class, 'unit_id');
    }
}
