<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payment_number',
        'order_id',
        'customer_id',
        'payment_method',
        'payment_gateway',
        'amount',
        'currency',
        'transaction_id',
        'status',
        'gateway_response',
        'refunded_amount',
        'refunded_at',
        'notes',
        'processed_by',
        'payment_date',
        'completed_at',
        'failed_at',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'payment_date' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];
}
