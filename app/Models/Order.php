<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'subtotal' => 'double',
        'discount_amount' => 'double',
        'tax_amount' => 'double',
        'shipping_amount' => 'double',
        'total_amount' => 'double',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public static function generateOrderNumber()
    {
        $year = date('Y');
        $lastOrder = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastOrder ? (int) substr($lastOrder->order_number, -6) + 1 : 1;
        
        return 'WS-' . $year . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }

    public function calculateTotals()
    {
        $subtotal = $this->items->sum('subtotal');
        $this->subtotal = $subtotal;
        $this->total_amount = $subtotal - $this->discount_amount + $this->tax_amount + $this->shipping_amount;
        return $this;
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed', 'processing']);
    }

    public function canBeRefunded()
    {
        return in_array($this->status, ['delivered', 'shipped']) && 
               $this->payments()->where('status', 'completed')->exists();
    }
}
