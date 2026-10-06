<?php

namespace Database\Seeders;

use App\Enums\StaffRole;
use App\Models\Staff;
use App\Modules\Triage\Models\Doctor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Enfermería
        Staff::updateOrCreate(
            ['email' => 'enfermera@medtriaje.test'],
            [
                'name' => 'Enfermera Demo',
                'password' => Hash::make('password123'),
                'role' => StaffRole::Nurse,
                'doctor_id' => null,
            ]
        );

        // 2. Recepción
        Staff::updateOrCreate(
            ['email' => 'recepcion@medtriaje.test'],
            [
                'name' => 'Recepcionista Demo',
                'password' => Hash::make('password123'),
                'role' => StaffRole::Reception,
                'doctor_id' => null,
            ]
        );

        // 3. Médico (vinculado a un doctor existente en triage_doctors)
        $doctor = Doctor::firstOrCreate(
            ['nombres' => 'Dr. Carlos Mendoza'],
            ['especialidad' => 'Medicina General']
        );

        Staff::updateOrCreate(
            ['email' => 'medico@medtriaje.test'],
            [
                'name' => 'Dr. Carlos Mendoza',
                'password' => Hash::make('password123'),
                'role' => StaffRole::Doctor,
                'doctor_id' => $doctor->id,
            ]
        );
    }
}
