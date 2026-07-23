<?php

namespace App\Modules\Triage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentDiagnosis extends Model
{
    protected $table = 'triage_appointment_diagnoses';

    protected $fillable = [
        'appointment_id',
        'cie10_code',
        'cie10_description',
        'diagnosis_type',
        'is_primary',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
