<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class OrderFulfillmentItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_fulfillment_id',
        'order_item_id',
        'product_id',
        'product_variant_id',
        'quantity',
    ];

    public function fulfillment()
    {
        return $this->belongsTo(OrderFulfillment::class, 'order_fulfillment_id');
    }
}
