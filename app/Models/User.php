<?php

namespace App\Models;

use App\Modules\Triage\Models\Appointment;
use App\Modules\Triage\Models\VitalSign;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $fillable = [
        'nombres',
        'cedula',
        'edad',
        'sexo',
        'contacto',
    ];

    public function vitalSigns(): HasMany
    {
        return $this->hasMany(VitalSign::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
