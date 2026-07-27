<?php

namespace App\Modules\Triage\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Modules\Triage\Models\VitalSign;
use App\Modules\Triage\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReceptionController extends Controller
{
    public function index(Request $request)
    {
        $patient = null;
        $pendingTriage = null;
        $userModel = config('triage.user_model');
        $idColumn = config('triage.user_id_column');
        $doctors = DB::table('triage_doctors')->get();

        if ($request->has('cedula') && $request->cedula !== '') {
            $patient = $userModel::where($idColumn, $request->cedula)->first();

            if ($patient) {
                $pendingTriage = VitalSign::where('user_id', $patient->id)
                    ->where('status', 'pending')
                    ->latest()
                    ->first();
            }
        }

        return view('triage.reception.index', compact('patient', 'pendingTriage', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:triage_doctors,id',
            'appointment_time' => 'required|in:09:00,09:30,10:00,10:30',
        ]);

        $appointmentDate = Carbon::today()->format('Y-m-d') . ' ' . $validated['appointment_time'] . ':00';

        if (Appointment::where('user_id', $validated['user_id'])
            ->whereDate('appointment_date', Carbon::today())
            ->exists()) {
            return back()->withInput()->withErrors([
                'appointment_time' => 'El paciente ya tiene una cita agendada para hoy.',
            ]);
        }

        if (Appointment::where('doctor_id', $validated['doctor_id'])
            ->where('appointment_date', $appointmentDate)
            ->exists()) {
            return back()->withInput()->withErrors([
                'appointment_time' => 'El médico ya tiene una cita asignada en ese horario.',
            ]);
        }

        // Step 1: Find the latest pending vital signs for this patient
        $pendingVitalSign = VitalSign::where('user_id', $validated['user_id'])
            ->where('status', 'pending')
            ->latest()
            ->first();

        // Step 2: Create the appointment with vital_signs_id (or null if none)
        $appointment = Appointment::create([
            'user_id' => $validated['user_id'],
            'doctor_id' => $validated['doctor_id'],
            'vital_signs_id' => $pendingVitalSign?->id,
            'appointment_date' => $appointmentDate,
            'status' => 'scheduled',
        ]);

        // Step 3: Update the vital sign record status to 'assigned'
        if ($pendingVitalSign) {
            $pendingVitalSign->update(['status' => 'assigned']);
        }

        $message = '¡Cita creada exitosamente!';
        if ($pendingVitalSign) {
            $message .= ' El triaje pendiente fue vinculado automáticamente.';
        }

        return redirect()->route('triage.reception.index')->with('success', $message);
    }
}
