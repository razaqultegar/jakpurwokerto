<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class District extends Model
{
    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = ['id', 'regency_id', 'name'];

    public $timestamps = false;

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
}
