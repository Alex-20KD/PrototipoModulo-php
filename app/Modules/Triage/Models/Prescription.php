<?php

namespace App\Modules\Triage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    protected $table = 'triage_prescriptions';

    protected $fillable = [
        'appointment_id',
        'generic_name',
        'concentration',
        'form',
        'quantity',
        'indications',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
