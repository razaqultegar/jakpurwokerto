<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberStatusLog extends Model
{
    protected $fillable = [
        'member_id',
        'from_status',
        'to_status',
        'reason',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
