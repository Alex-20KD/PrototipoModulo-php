<?php

namespace Tests\Feature\Api;

use App\Enums\StaffRole;
use App\Models\Staff;
use App\Models\User;
use App\Modules\Triage\Models\Appointment;
use App\Modules\Triage\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReceptionApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $receptionStaff = Staff::create([
            'name' => 'Recepcionista Test',
            'email' => 'reception_test@example.com',
            'password' => 'secret',
            'role' => StaffRole::Reception,
        ]);

        Sanctum::actingAs($receptionStaff);
    }

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

    public function test_duplicate_doctor_slot_returns_409(): void
    {
        $patientA = User::create([
            'nombres' => 'Paciente A',
            'cedula' => '3333333333',
            'edad' => 30,
            'sexo' => 'Masculino',
        ]);

        $patientB = User::create([
            'nombres' => 'Paciente B',
            'cedula' => '4444444444',
            'edad' => 35,
            'sexo' => 'Femenino',
        ]);

        $doctor = Doctor::create([
            'nombres' => 'Dr. Cupo',
            'especialidad' => 'General',
        ]);

        $first = $this->postJson('/api/reception/appointments', [
            'user_id' => $patientA->id,
            'doctor_id' => $doctor->id,
            'appointment_time' => '10:00',
        ]);

        $first->assertStatus(201);

        $second = $this->postJson('/api/reception/appointments', [
            'user_id' => $patientB->id,
            'doctor_id' => $doctor->id,
            'appointment_time' => '10:00',
        ]);

        $second->assertStatus(409)
            ->assertJsonStructure(['status', 'message', 'data'])
            ->assertJsonPath('status', false);

        $this->assertDatabaseCount('triage_appointments', 1);
    }

    public function test_patient_cannot_have_two_appointments_same_day_returns_409(): void
    {
        $patient = User::create([
            'nombres' => 'Paciente Unico',
            'cedula' => '5555555555',
            'edad' => 28,
            'sexo' => 'Femenino',
        ]);

        $doctorA = Doctor::create([
            'nombres' => 'Dr. Uno',
            'especialidad' => 'General',
        ]);

        $doctorB = Doctor::create([
            'nombres' => 'Dr. Dos',
            'especialidad' => 'Interna',
        ]);

        $first = $this->postJson('/api/reception/appointments', [
            'user_id' => $patient->id,
            'doctor_id' => $doctorA->id,
            'appointment_time' => '09:00',
        ]);

        $first->assertStatus(201);

        $second = $this->postJson('/api/reception/appointments', [
            'user_id' => $patient->id,
            'doctor_id' => $doctorB->id,
            'appointment_time' => '10:00',
        ]);

        $second->assertStatus(409)
            ->assertJsonPath('status', false);

        $this->assertDatabaseCount('triage_appointments', 1);
    }

    public function test_database_rejects_duplicate_doctor_slot(): void
    {
        $patientA = User::create([
            'nombres' => 'Paciente Concurrencia A',
            'cedula' => '6666666666',
            'edad' => 41,
            'sexo' => 'Masculino',
        ]);

        $patientB = User::create([
            'nombres' => 'Paciente Concurrencia B',
            'cedula' => '7777777777',
            'edad' => 52,
            'sexo' => 'Femenino',
        ]);

        $doctor = Doctor::create([
            'nombres' => 'Dr. Restriccion',
            'especialidad' => 'General',
        ]);

        $appointmentDate = Carbon::today()->format('Y-m-d').' 09:00:00';

        Appointment::create([
            'user_id' => $patientA->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $appointmentDate,
            'status' => 'scheduled',
        ]);

        try {
            Appointment::create([
                'user_id' => $patientB->id,
                'doctor_id' => $doctor->id,
                'appointment_date' => $appointmentDate,
                'status' => 'scheduled',
            ]);

            $this->fail('Se esperaba una violación de unicidad del cupo médico.');
        } catch (QueryException $e) {
            $this->assertSame('23000', $e->getCode());
        }

        $this->assertDatabaseCount('triage_appointments', 1);
    }

    public function test_database_rejects_second_appointment_same_patient_same_day(): void
    {
        $patient = User::create([
            'nombres' => 'Paciente Dia',
            'cedula' => '1010101010',
            'edad' => 37,
            'sexo' => 'Masculino',
        ]);

        $doctorA = Doctor::create([
            'nombres' => 'Dr. Dia Uno',
            'especialidad' => 'General',
        ]);

        $doctorB = Doctor::create([
            'nombres' => 'Dr. Dia Dos',
            'especialidad' => 'Interna',
        ]);

        $today = Carbon::today()->format('Y-m-d');

        Appointment::create([
            'user_id' => $patient->id,
            'doctor_id' => $doctorA->id,
            'appointment_date' => $today.' 09:00:00',
            'status' => 'scheduled',
        ]);

        try {
            Appointment::create([
                'user_id' => $patient->id,
                'doctor_id' => $doctorB->id,
                'appointment_date' => $today.' 11:00:00',
                'status' => 'scheduled',
            ]);

            $this->fail('Se esperaba una violación de unicidad de cita por día.');
        } catch (QueryException $e) {
            $this->assertSame('23000', $e->getCode());
        }

        $this->assertDatabaseCount('triage_appointments', 1);
    }
}
