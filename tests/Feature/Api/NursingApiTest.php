<?php

namespace Tests\Feature\Api;

use App\Enums\StaffRole;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NursingApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $nurseStaff = Staff::create([
            'name' => 'Enfermera Test',
            'email' => 'nurse_test@example.com',
            'password' => 'secret',
            'role' => StaffRole::Nurse,
        ]);

        Sanctum::actingAs($nurseStaff);
    }

    public function test_can_create_vital_signs(): void
    {
        $patient = User::create([
            'nombres' => 'Paciente de Prueba',
            'cedula' => '9999999999',
            'edad' => 30,
            'sexo' => 'Masculino',
        ]);

        $payload = [
            'user_id' => $patient->id,
            'blood_pressure' => '120/80',
            'heart_rate' => 80,
            'weight_kg' => 70.5,
            'height_cm' => 175,
            'temperature' => 36.5,
            'respiratory_rate' => 16,
            'reason_for_consultation' => 'Dolor de cabeza',
        ];

        $response = $this->postJson('/api/triage/vital-signs', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'blood_pressure',
                    'heart_rate',
                    'weight_kg',
                    'height_cm',
                    'temperature',
                    'respiratory_rate',
                    'reason_for_consultation',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonPath('status', true);

        $this->assertDatabaseHas('triage_vital_signs', [
            'user_id' => $patient->id,
            'blood_pressure' => '120/80',
            'status' => 'pending',
        ]);
    }

    public function test_validates_vital_signs_input(): void
    {
        $payload = [
            // faltan campos obligatorios
            'blood_pressure' => '120-80', // formato inválido
            'heart_rate' => 10, // muy bajo
        ];

        $response = $this->postJson('/api/triage/vital-signs', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user_id',
                    'blood_pressure',
                    'heart_rate',
                    'weight_kg',
                    'height_cm',
                    'temperature',
                    'respiratory_rate',
                    'reason_for_consultation',
                ],
            ])
            ->assertJsonPath('status', false);
    }
}
