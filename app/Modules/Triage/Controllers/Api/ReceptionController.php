<?php

namespace App\Modules\Triage\Controllers\Api;

use App\Modules\Triage\Models\Appointment;
use App\Modules\Triage\Models\VitalSign;
use App\Modules\Triage\Requests\StoreAppointmentApiRequest;
use App\Modules\Triage\Resources\AppointmentResource;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReceptionController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = Appointment::with('vitalSigns')->latest('appointment_date');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query->get();

        return $this->ok(
            'Citas médicas obtenidas con éxito',
            AppointmentResource::collection($appointments)
        );
    }

    public function store(StoreAppointmentApiRequest $request)
    {
        $validated = $request->validated();

        $appointmentDate = Carbon::today()->format('Y-m-d').' '.$validated['appointment_time'].':00';

        // Find the latest pending vital signs for this patient
        $pendingVitalSign = VitalSign::where('user_id', $validated['user_id'])
            ->where('status', 'pending')
            ->latest()
            ->first();

        $appointment = Appointment::create([
            'user_id' => $validated['user_id'],
            'doctor_id' => $validated['doctor_id'],
            'vital_signs_id' => $pendingVitalSign?->id,
            'appointment_date' => $appointmentDate,
            'status' => 'scheduled',
        ]);

        if ($pendingVitalSign) {
            $pendingVitalSign->update(['status' => 'assigned']);
        }

        $message = 'Cita médica agendada con éxito.';
        if ($pendingVitalSign) {
            $message .= ' El triaje pendiente fue vinculado automáticamente.';
        }

        $appointment->load('vitalSigns');

        return $this->created(
            $message,
            new AppointmentResource($appointment)
        );
    }
}
