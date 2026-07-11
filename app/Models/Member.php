<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'registration_number',
        'card_number',
        'nik',
        'name',
        'pob',
        'dob',
        'gender',
        'blood_type',
        'shirt_size',
        'address_street',
        'district_id',
        'regency_id',
        'province_id',
        'phone',
        'email',
        'status',
        'valid_from',
        'valid_until',
        'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date',
            'valid_from' => 'date',
            'valid_until' => 'date',
            'registered_at' => 'datetime',
        ];
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(MemberRegistration::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(MemberStatusLog::class)->latest();
    }

    public function latestRegistration()
    {
        return $this->hasOne(MemberRegistration::class)->latestOfMany('registered_at');
    }
}
