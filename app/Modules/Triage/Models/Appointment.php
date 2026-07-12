<?php

namespace App\Modules\Triage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    protected $table = 'triage_appointments';

    protected $fillable = [
        'user_id',
        'doctor_id',
        'vital_signs_id',
        'appointment_date',
        'status',
        'anamnesis',
        'antecedentes',
        'cie10_code',
        'cie10_description',
        'diagnosis_type',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
    ];

    // References host system's users table
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function vitalSigns(): BelongsTo
    {
        return $this->belongsTo(VitalSign::class, 'vital_signs_id');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
}

