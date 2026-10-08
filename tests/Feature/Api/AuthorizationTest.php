<?php

namespace Tests\Feature\Api;

use App\Enums\StaffRole;
use App\Models\Staff;
use App\Models\User;
use App\Modules\Triage\Models\Appointment;
use App\Modules\Triage\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_up_is_public()
    {
        $response = $this->get('/up');
        $response->assertStatus(200);
    }

    public function test_ping_is_public()
    {
        $response = $this->getJson('/api/ping');
        $response->assertStatus(200);
    }

    public function test_protected_route_without_token_returns_401()
    {
        $response = $this->getJson('/api/doctor/appointments');

        $response->assertStatus(401)
            ->assertJsonPath('status', false)
            ->assertJsonPath('message', 'No autenticado');
    }

    public function test_request_with_wrong_role_returns_403()
    {
        $staff = Staff::create([
            'name' => 'Nurse',
            'email' => 'nurse@example.com',
            'password' => 'secret',
            'role' => StaffRole::Nurse,
        ]);

        $response = $this->actingAs($staff, 'sanctum')
            ->getJson('/api/doctor/appointments');

        $response->assertStatus(403)
            ->assertJsonPath('status', false)
            ->assertJsonPath('message', 'No tiene permisos para acceder a este recurso');
    }

    public function test_doctor_viewing_own_appointments_returns_200()
    {
        $doctor = Doctor::create(['nombres' => 'Dr', 'apellidos' => 'House', 'especialidad' => 'General']);
        $staff = Staff::create([
            'name' => 'Doctor House',
            'email' => 'house@example.com',
            'password' => 'secret',
            'role' => StaffRole::Doctor,
            'doctor_id' => $doctor->id,
        ]);

        $response = $this->actingAs($staff, 'sanctum')
            ->getJson('/api/doctor/appointments');

        $response->assertStatus(200)
            ->assertJsonPath('status', true);
    }

    public function test_doctor_viewing_other_doctor_appointment_pdf_returns_403()
    {
        $doctor1 = Doctor::create(['nombres' => 'Dr', 'apellidos' => 'House', 'especialidad' => 'General']);
        $doctor2 = Doctor::create(['nombres' => 'Dr', 'apellidos' => 'Strange', 'especialidad' => 'General']);

        $staff = Staff::create([
            'name' => 'Doctor House',
            'email' => 'house2@example.com',
            'password' => 'secret',
            'role' => StaffRole::Doctor,
            'doctor_id' => $doctor1->id,
        ]);

        $user = User::create(['nombres' => 'Juan', 'cedula' => '1234567890', 'edad' => 30, 'sexo' => 'M', 'contacto' => '123']);

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'doctor_id' => $doctor2->id,
            'appointment_date' => Carbon::now(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($staff, 'sanctum')
            ->getJson('/api/doctor/pdf/'.$appointment->id);

        $response->assertStatus(403)
            ->assertJsonPath('status', false);
    }

    public function test_doctor_viewing_own_appointment_pdf_returns_200()
    {
        Storage::fake('pc5');

        $doctor = Doctor::create(['nombres' => 'Dr', 'apellidos' => 'House', 'especialidad' => 'General']);

        $staff = Staff::create([
            'name' => 'Doctor House',
            'email' => 'house3@example.com',
            'password' => 'secret',
            'role' => StaffRole::Doctor,
            'doctor_id' => $doctor->id,
        ]);

        $user = User::create(['nombres' => 'Juan', 'cedula' => '1234567890', 'edad' => 30, 'sexo' => 'M', 'contacto' => '123']);

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => Carbon::now(),
            'status' => 'completed',
        ]);

        $response = $this->actingAs($staff, 'sanctum')
            ->get('/api/doctor/pdf/'.$appointment->id);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_history_endpoint_protected_by_role()
    {
        $doctor = Doctor::create(['nombres' => 'Dr', 'apellidos' => 'House', 'especialidad' => 'General']);
        $staffDoc = Staff::create([
            'name' => 'Doctor House',
            'email' => 'house4@example.com',
            'password' => 'secret',
            'role' => StaffRole::Doctor,
            'doctor_id' => $doctor->id,
        ]);

        $staffNurse = Staff::create([
            'name' => 'Nurse 2',
            'email' => 'nurse2@example.com',
            'password' => 'secret',
            'role' => StaffRole::Nurse,
        ]);

        $user = User::create(['nombres' => 'Juan', 'cedula' => '1234567890', 'edad' => 30, 'sexo' => 'M', 'contacto' => '123']);

        // Enfermera intentando acceder (Falla por Middleware de Ruta role:doctor)
        $response = $this->actingAs($staffNurse, 'sanctum')
            ->getJson('/api/patients/'.$user->id.'/history');

        $response->assertStatus(403);

        // Médico intentando acceder (Pasa el Middleware y el Policy de history)
        $response = $this->actingAs($staffDoc, 'sanctum')
            ->getJson('/api/patients/'.$user->id.'/history');

        $response->assertStatus(200)
            ->assertJsonPath('status', true);
    }
}
