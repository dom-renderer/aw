<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function getRelatedModel()
    {
        if (!$this->related_eloquent || !$this->related_eloquent_id) {
            return null;
        }

        return $this->related_eloquent::find($this->related_eloquent_id);
    }

    public function getRelatedOrder()
    {
        if ($this->related_eloquent === 'App\Models\Order' || $this->related_eloquent === Order::class) {
            return Order::find($this->related_eloquent_id);
        }
        return null;
    }

    public function getRelatedOrderItem()
    {
        if ($this->related_eloquent === 'App\Models\OrderItem' || $this->related_eloquent === OrderItem::class) {
            return OrderItem::find($this->related_eloquent_id);
        }
        return null;
    }
}
