<?php

namespace App\Modules\Triage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    protected $table = 'triage_appointments';

    const DIAGNOSIS_TYPES = [
        'presuntivo_ingreso' => 'Presuntivo de Ingreso',
        'definitivo_ingreso' => 'Definitivo de Ingreso',
        'presuntivo_alta'    => 'Presuntivo de Alta',
        'definitivo_alta'    => 'Definitivo de Alta',
    ];

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
        'ant_hta',
        'ant_hta_years',
        'ant_hta_treatment',
        'ant_hta_medication',
        'ant_dm',
        'ant_dm_years',
        'ant_dm_treatment',
        'ant_dm_medication',
        'ant_chronic',
        'ant_chronic_other',
        'ant_observations',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'ant_chronic' => 'array',
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

    public function getDiagnosisTypeLabelAttribute(): string
    {
        return self::DIAGNOSIS_TYPES[$this->diagnosis_type] ?? 'No especificado';
    }
}

