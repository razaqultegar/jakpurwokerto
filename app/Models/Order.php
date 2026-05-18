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
        'customer_note',
        'item',
        'subtotal',
        'amount_due',
        'payment_type',
        'payment_method_type',
        'payment_method_key',
        'payment_data',
        'status',
    ];

    protected $casts = [
        'item' => 'array',
        'payment_data' => 'array',
        'subtotal' => 'integer',
        'amount_due' => 'integer',
    ];

    public function getRouteKeyName()
    {
        return 'order_id';
    }
}
