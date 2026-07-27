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
        $patient1 = User::updateOrCreate(
            ['cedula' => '0102030405'],
            [
                'nombres' => 'María García López',
                'edad' => 34,
                'sexo' => 'Femenino',
                'contacto' => '0991234567',
            ],
        );

        User::updateOrCreate(
            ['cedula' => '0605040302'],
            [
                'nombres' => 'Juan Pérez Martínez',
                'edad' => 45,
                'sexo' => 'Masculino',
                'contacto' => '0987654321',
            ],
        );

        User::updateOrCreate(
            ['cedula' => '1710203040'],
            [
                'nombres' => 'Ana Torres Ruiz',
                'edad' => 28,
                'sexo' => 'Femenino',
                'contacto' => '0976543210',
            ],
        );

        Doctor::updateOrCreate(
            ['nombres' => 'Dr. Carlos Mendoza'],
            ['especialidad' => 'Medicina General'],
        );

        Doctor::updateOrCreate(
            ['nombres' => 'Dra. Lucía Ramírez'],
            ['especialidad' => 'Medicina Interna'],
        );

        VitalSign::updateOrCreate(
            [
                'user_id' => $patient1->id,
                'status' => 'pending',
            ],
            [
                'blood_pressure' => '120/80',
                'weight_kg' => 65.50,
                'height_cm' => 160.00,
                'temperature' => 36.5,
                'heart_rate' => 72,
                'respiratory_rate' => 16,
                'reason_for_consultation' => 'Dolor de cabeza persistente desde hace 3 días',
            ],
        );

        $this->call(Cie10Seeder::class);
        $this->call(MedicationSeeder::class);
    }
}
