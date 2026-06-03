<?php

namespace App\Modules\Triage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    protected $table = 'triage_doctors';

    protected $fillable = [
        'nombres',
        'especialidad',
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
