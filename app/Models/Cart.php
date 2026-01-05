<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'session_id',
        'warehouse_id',
        'abandoned_at',
        'converted_to_order_id',
    ];

    protected $casts = [
        'abandoned_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
