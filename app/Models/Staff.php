<?php

namespace App\Models;

use App\Enums\StaffRole;
use App\Modules\Triage\Models\Doctor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use InvalidArgumentException;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'staff';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'doctor_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => StaffRole::class,
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Staff $staff): void {
            $role = $staff->role instanceof StaffRole
                ? $staff->role
                : StaffRole::tryFrom((string) $staff->role);

            if ($role === StaffRole::Doctor && is_null($staff->doctor_id)) {
                throw new InvalidArgumentException('El personal con rol de médico requiere un doctor_id asignado.');
            }
        });
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function isDoctor(): bool
    {
        return $this->role === StaffRole::Doctor;
    }

    public function isNurse(): bool
    {
        return $this->role === StaffRole::Nurse;
    }

    public function isReception(): bool
    {
        return $this->role === StaffRole::Reception;
    }
}
