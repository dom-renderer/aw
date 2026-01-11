<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'from_status',
        'to_status',
        'notes',
        'changed_by',
        'created_at',
    ];
}
