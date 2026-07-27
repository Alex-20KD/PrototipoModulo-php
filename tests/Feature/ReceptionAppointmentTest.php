<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Triage\Models\Appointment;
use App\Modules\Triage\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceptionAppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_patient_cannot_have_two_appointments_on_the_same_day(): void
    {
        $patient = User::create([
            'nombres' => 'Paciente de Prueba',
            'cedula' => '9999999999',
            'edad' => 30,
            'sexo' => 'Femenino',
        ]);

        $doctor = Doctor::create([
            'nombres' => 'Doctora de Prueba',
            'especialidad' => 'Medicina General',
        ]);

        Appointment::create([
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => Carbon::today()->setTime(9, 0),
            'status' => 'scheduled',
        ]);

        $response = $this->post(route('triage.reception.store'), [
            'user_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_time' => '10:00',
        ]);

        $response->assertSessionHasErrors('appointment_time');
        $this->assertDatabaseCount('triage_appointments', 1);
    }
}
