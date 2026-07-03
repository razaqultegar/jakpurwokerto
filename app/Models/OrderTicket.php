<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTicket extends Model
{
    protected $fillable = [
        'order_id',
        'code',
        'unit_index',
        'checked_in_at',
    ];

    protected $casts = [
        'unit_index' => 'integer',
        'checked_in_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
