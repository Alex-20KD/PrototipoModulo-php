<?php

namespace App\Enums;

enum StaffRole: string
{
    case Nurse = 'nurse';
    case Reception = 'reception';
    case Doctor = 'doctor';

    public function label(): string
    {
        return match ($this) {
            self::Nurse => 'Enfermería',
            self::Reception => 'Recepción',
            self::Doctor => 'Médico',
        };
    }
}
