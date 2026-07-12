<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Modules\Triage\Models\Doctor;
use App\Modules\Triage\Models\VitalSign;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 3 sample patients
        $patient1 = User::create([
            'nombres' => 'María García López',
            'cedula' => '0102030405',
            'edad' => 34,
            'sexo' => 'Femenino',
            'contacto' => '0991234567',
        ]);

        $patient2 = User::create([
            'nombres' => 'Juan Pérez Martínez',
            'cedula' => '0605040302',
            'edad' => 45,
            'sexo' => 'Masculino',
            'contacto' => '0987654321',
        ]);

        $patient3 = User::create([
            'nombres' => 'Ana Torres Ruiz',
            'cedula' => '1710203040',
            'edad' => 28,
            'sexo' => 'Femenino',
            'contacto' => '0976543210',
        ]);

        // 2 doctors
        Doctor::create([
            'nombres' => 'Dr. Carlos Mendoza',
            'especialidad' => 'Medicina General',
        ]);

        Doctor::create([
            'nombres' => 'Dra. Lucía Ramírez',
            'especialidad' => 'Medicina Interna',
        ]);

        // 1 pending vital sign record for patient 1
        VitalSign::create([
            'user_id' => $patient1->id,
            'blood_pressure' => '120/80',
            'weight_kg' => 65.50,
            'height_cm' => 160.00,
            'temperature' => 36.5,
            'heart_rate' => 72,
            'reason_for_consultation' => 'Dolor de cabeza persistente desde hace 3 días',
            'status' => 'pending',
        ]);

        // CIE-10 catalog
        $this->call(Cie10Seeder::class);
    }
}
