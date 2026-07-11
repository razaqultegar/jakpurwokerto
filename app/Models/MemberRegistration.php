<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberRegistration extends Model
{
    protected $fillable = [
        'member_id',
        'registration_type', // Baru / Perpanjang
        'sector',
        'registration_number',
        'card_number',
        'valid_from',
        'valid_until',
        'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'valid_from'   => 'date',
            'valid_until'  => 'date',
            'registered_at'=> 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
