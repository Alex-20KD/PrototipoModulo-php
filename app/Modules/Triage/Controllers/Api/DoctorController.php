<?php

namespace App\Modules\Triage\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Triage\Models\Appointment;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DoctorController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Appointment::class);

        $doctor_id = $request->user()->doctor_id;

        $appointments = Appointment::with(['user', 'vitalSigns'])
            ->where('doctor_id', $doctor_id)
            ->whereDate('appointment_date', today())
            ->get();

        return $this->ok('Citas listadas', $appointments);
    }

    public function pdf(Appointment $appointment)
    {
        Gate::authorize('downloadPdf', $appointment);

        // En un entorno real se descargaría un archivo PDF o se devolvería un enlace
        return $this->ok('PDF generado', ['url' => 'http://example.com/pdf']);
    }

    public function history($user_id)
    {
        Gate::authorize('history', Appointment::class);

        $patient = User::findOrFail($user_id);

        $appointments = Appointment::with(['vitalSigns', 'prescriptions', 'diagnoses'])
            ->where('user_id', $user_id)
            ->orderBy('appointment_date', 'desc')
            ->get();

        return $this->ok('Historial clínico', $appointments);
    }
}
