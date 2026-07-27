<?php

namespace App\Modules\Triage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VitalSign extends Model
{
    protected $table = 'triage_vital_signs';

    protected $fillable = [
        'user_id',
        'blood_pressure',
        'weight_kg',
        'height_cm',
        'temperature',
        'heart_rate',
        'respiratory_rate',
        'reason_for_consultation',
        'status',
    ];

    // References host system's users table
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function appointment(): HasOne
    {
        return $this->hasOne(Appointment::class, 'vital_signs_id');
    }
}
