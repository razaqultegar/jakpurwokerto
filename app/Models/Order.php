<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_method',
        'pickup_location',
        'customer_address',
        'payment_proof',
        'shipping_tracking',
        'dp_settlement_proof',
        'item',
        'subtotal',
        'amount_due',
        'payment_type',
        'payment_method_type',
        'payment_method_key',
        'payment_data',
        'status',
        'verified_at',
        'completed_at',
    ];

    protected $casts = [
        'item' => 'array',
        'payment_data' => 'array',
        'subtotal' => 'integer',
        'amount_due' => 'integer',
        'verified_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'order_id';
    }
}
