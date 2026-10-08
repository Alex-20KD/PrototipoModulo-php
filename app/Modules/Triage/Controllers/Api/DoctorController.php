<?php

namespace App\Modules\Triage\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Triage\Models\Appointment;
use App\Services\ClinicalReportStorage;
use App\Traits\ApiResponseTrait;
use Barryvdh\DomPDF\Facade\Pdf;
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

    public function pdf(Appointment $appointment, ClinicalReportStorage $storage)
    {
        Gate::authorize('downloadPdf', $appointment);

        if ($appointment->status !== 'completed') {
            return $this->error('La cita aún no está completada', 400);
        }

        // Regenerate if report_path is null or doesn't exist on disk
        if (empty($appointment->report_path) || ! $storage->exists($appointment->report_path)) {
            $appointment->loadMissing(['user', 'doctor', 'vitalSigns', 'prescriptions', 'diagnoses']);
            $pdfContent = Pdf::loadView('triage.pdf.formulario002', compact('appointment'))->output();

            $path = $storage->path($appointment);
            $storage->put($path, $pdfContent);

            $appointment->update(['report_path' => $path]);
        }

        // Stream the file directly from SFTP to the client
        $stream = $storage->readStream($appointment->report_path);

        return response()->stream(
            function () use ($stream) {
                fpassthru($stream);
                fclose($stream);
            },
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="formulario002.pdf"',
            ]
        );
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
