<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'quantity' => 'double',
        'price_per_unit' => 'double',
        'discount_amount' => 'double',
        'subtotal' => 'double',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function bundleItems()
    {
        return $this->hasMany(OrderBundleItem::class, 'order_item_id');
    }

    public function baseUnit()
    {
        return $this->belongsTo(ProductBaseUnit::class, 'unit_id')
            ->where('unit_type', 0);
    }

    public function additionalUnit()
    {
        return $this->belongsTo(ProductAdditionalUnit::class, 'unit_id')
            ->where('unit_type', 1);
    }

    public function getUnit()
    {
        return $this->unit_type == 0
            ? $this->belongsTo(ProductBaseUnit::class, 'unit_id')
            : $this->belongsTo(ProductAdditionalUnit::class, 'unit_id');
    }

    public function calculateSubtotal()
    {
        $this->subtotal = ($this->quantity * $this->price_per_unit) - $this->discount_amount;
        return $this;
    }
}
