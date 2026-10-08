<?php

namespace Tests\Feature\Api;

use App\Enums\StaffRole;
use App\Models\Staff;
use App\Models\User;
use App\Modules\Triage\Models\Appointment;
use App\Modules\Triage\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToWriteFile;
use Tests\TestCase;

class PdfDownloadTest extends TestCase
{
    use RefreshDatabase;

    private function createAppointmentForDoctor()
    {
        $doctor = Doctor::create(['nombres' => 'Dr', 'apellidos' => 'Strange', 'especialidad' => 'Neurologia']);
        $staff = Staff::create([
            'name' => 'Doctor Strange',
            'email' => 'strange@example.com',
            'password' => 'secret',
            'role' => StaffRole::Doctor,
            'doctor_id' => $doctor->id,
        ]);
        $user = User::create(['nombres' => 'Patient', 'cedula' => '0987654321', 'edad' => 25, 'sexo' => 'M', 'contacto' => '123']);

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now(),
            'status' => 'completed',
        ]);

        return [$staff, $appointment];
    }

    public function test_unauthenticated_user_cannot_download_pdf(): void
    {
        $response = $this->getJson('/api/doctor/pdf/1');

        $response->assertStatus(401)
            ->assertJsonPath('status', false)
            ->assertJsonPath('message', 'No autenticado');
    }

    public function test_doctor_can_download_own_pdf(): void
    {
        Storage::fake('pc5');
        [$staff, $appointment] = $this->createAppointmentForDoctor();

        $response = $this->actingAs($staff, 'sanctum')->get('/api/doctor/pdf/'.$appointment->id);

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_doctor_cannot_download_other_doctors_pdf(): void
    {
        [$staff, $appointment] = $this->createAppointmentForDoctor();

        // Create another doctor
        $otherDoctor = Doctor::create(['nombres' => 'Dr', 'apellidos' => 'Who', 'especialidad' => 'Tiempo']);
        $otherStaff = Staff::create([
            'name' => 'Doctor Who',
            'email' => 'who@example.com',
            'password' => 'secret',
            'role' => StaffRole::Doctor,
            'doctor_id' => $otherDoctor->id,
        ]);

        $response = $this->actingAs($otherStaff, 'sanctum')->getJson('/api/doctor/pdf/'.$appointment->id);

        $response->assertStatus(403)
            ->assertJsonPath('status', false)
            ->assertJsonPath('message', 'This action is unauthorized.');
    }

    public function test_returns_503_if_sftp_is_down(): void
    {
        [$staff, $appointment] = $this->createAppointmentForDoctor();

        // Mock Storage to throw exception
        Storage::shouldReceive('disk')
            ->with('pc5')
            ->andThrow(new UnableToWriteFile('Simulated connection failure'));

        $response = $this->actingAs($staff, 'sanctum')->getJson('/api/doctor/pdf/'.$appointment->id);

        $response->assertStatus(503)
            ->assertJsonPath('status', false)
            ->assertJsonPath('message', 'Servicio de almacenamiento no disponible temporalmente. Intente más tarde.');
    }

    public function test_other_endpoints_still_work_when_sftp_is_down(): void
    {
        [$staff, $appointment] = $this->createAppointmentForDoctor();

        // Mock Storage to throw exception for anything related to pc5
        Storage::shouldReceive('disk')
            ->with('pc5')
            ->andThrow(new UnableToWriteFile('Simulated connection failure'));

        // Ping endpoint should still work perfectly
        $response = $this->getJson('/api/ping');
        $response->assertStatus(200);

        // Fetching appointments should still work perfectly
        $response = $this->actingAs($staff, 'sanctum')->getJson('/api/doctor/appointments');
        $response->assertStatus(200)
            ->assertJsonPath('status', true);
    }
}
