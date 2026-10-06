<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Modules\Triage\Models\Appointment;
use App\Modules\Triage\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceptionApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_appointments(): void
    {
        $patient = User::create([
            'nombres' => 'Paciente Listar',
            'cedula' => '1111111111',
            'edad' => 40,
            'sexo' => 'Masculino',
        ]);

        $doctor = Doctor::create([
            'nombres' => 'Dr. Listar',
            'especialidad' => 'General',
        ]);

        Appointment::create([
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => Carbon::today()->format('Y-m-d').' 09:00:00',
            'status' => 'scheduled',
        ]);

        $response = $this->getJson('/api/reception/appointments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'doctor_id',
                        'vital_signs_id',
                        'appointment_date',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonPath('status', true);
    }

    public function test_can_create_appointment(): void
    {
        $patient = User::create([
            'nombres' => 'Paciente Crear',
            'cedula' => '2222222222',
            'edad' => 25,
            'sexo' => 'Femenino',
        ]);

        $doctor = Doctor::create([
            'nombres' => 'Dra. Crear',
            'especialidad' => 'General',
        ]);

        $payload = [
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_time' => '10:00',
        ];

        $response = $this->postJson('/api/reception/appointments', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'doctor_id',
                    'appointment_date',
                    'status',
                ],
            ])
            ->assertJsonPath('status', true);

        $this->assertDatabaseHas('triage_appointments', [
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'status' => 'scheduled',
        ]);
    }

    public function test_validates_appointment_creation(): void
    {
        $payload = [
            'appointment_time' => '15:00', // invalid time and missing required fields
        ];

        $response = $this->postJson('/api/reception/appointments', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user_id',
                    'doctor_id',
                    'appointment_time',
                ],
            ])
            ->assertJsonPath('status', false);
    }
}
