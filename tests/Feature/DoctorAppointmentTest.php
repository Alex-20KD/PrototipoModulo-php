<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Triage\Controllers\DoctorController;
use App\Modules\Triage\Models\Appointment;
use App\Modules\Triage\Models\Cie10;
use App\Modules\Triage\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class DoctorAppointmentTest extends TestCase
{
    use RefreshDatabase;

    private function appointmentPayload(array $overrides = []): array
    {
        return array_merge([
            'anamnesis' => 'El paciente refiere cefalea intensa de dos horas de evolución.',
            'diagnoses' => [
                [
                    'cie10_code' => 'R51',
                    'diagnosis_type' => 'presuntivo_ingreso',
                    'is_primary' => true,
                ],
            ],
            'ant_hta' => false,
            'ant_dm' => false,
        ], $overrides);
    }

    private function makeScheduledAppointment(): array
    {
        $patient = User::create([
            'nombres' => 'Paciente Consulta',
            'cedula' => '1212121212',
            'edad' => 45,
            'sexo' => 'Masculino',
        ]);

        $doctor = Doctor::create([
            'nombres' => 'Dr. Consulta',
            'especialidad' => 'Medicina General',
        ]);

        Cie10::create([
            'code' => 'R51',
            'description' => 'Cefalea',
        ]);

        $appointment = Appointment::create([
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => Carbon::today()->setTime(9, 0),
            'status' => 'scheduled',
        ]);

        return [$appointment, $patient, $doctor];
    }

    public function test_second_attempt_to_complete_appointment_returns_409(): void
    {
        [$appointment] = $this->makeScheduledAppointment();

        $first = $this->post(
            route('triage.doctor.attend.store', $appointment),
            $this->appointmentPayload()
        );

        $first->assertStatus(302);

        $this->assertDatabaseHas('triage_appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseCount('triage_appointment_diagnoses', 1);

        $second = $this->post(
            route('triage.doctor.attend.store', $appointment),
            $this->appointmentPayload()
        );

        $second->assertStatus(409);
        $this->assertDatabaseCount('triage_appointment_diagnoses', 1);
    }

    public function test_completed_appointment_rejects_immediately_with_409(): void
    {
        [$appointment] = $this->makeScheduledAppointment();

        DB::table('triage_appointments')
            ->where('id', $appointment->id)
            ->update(['status' => 'completed']);

        $response = $this->post(
            route('triage.doctor.attend.store', $appointment),
            $this->appointmentPayload()
        );

        $response->assertStatus(409);
        $this->assertDatabaseCount('triage_appointment_diagnoses', 0);
        $this->assertDatabaseHas('triage_appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
        ]);
    }

    public function test_revalidates_status_under_lock_inside_transaction(): void
    {
        [$appointment] = $this->makeScheduledAppointment();

        $stale = Appointment::find($appointment->id);
        $stale->status = 'scheduled';

        DB::table('triage_appointments')
            ->where('id', $appointment->id)
            ->update(['status' => 'completed']);

        $request = Request::create(
            route('triage.doctor.attend.store', $appointment),
            'POST',
            $this->appointmentPayload()
        );
        $request->headers->set('Accept', 'application/json');

        try {
            app(DoctorController::class)->store($request, $stale);
            $this->fail('Se esperaba un conflicto 409 al revalidar el estado bajo bloqueo.');
        } catch (HttpException $e) {
            $this->assertSame(409, $e->getStatusCode());
        }

        $this->assertDatabaseCount('triage_appointment_diagnoses', 0);
        $this->assertDatabaseHas('triage_appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
        ]);
    }
}
