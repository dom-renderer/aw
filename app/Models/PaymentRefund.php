<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PaymentRefund extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'refund_number',
        'payment_id',
        'order_id',
        'amount',
        'reason',
        'status',
        'transaction_id',
        'gateway_response',
        'processed_by',
        'notes',
        'refund_date',
        'completed_at',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'refund_date' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
