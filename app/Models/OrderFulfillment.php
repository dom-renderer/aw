<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class OrderFulfillment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'fulfillment_number',
        'warehouse_id',
        'status',
        'tracking_number',
        'carrier',
        'shipping_method',
        'tracking_url',
        'processed_by',
        'packed_by',
        'shipped_by',
        'notes',
        'packed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'packed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(OrderFulfillmentItem::class);
    }
}
