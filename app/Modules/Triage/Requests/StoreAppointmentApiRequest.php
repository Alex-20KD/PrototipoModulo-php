<?php

namespace App\Modules\Triage\Requests;

use App\Modules\Triage\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAppointmentApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'doctor_id' => ['required', 'integer', 'exists:triage_doctors,id'],
            'appointment_time' => ['required', 'string', 'in:09:00,09:30,10:00,10:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El ID del paciente es obligatorio.',
            'user_id.exists' => 'El paciente no existe en el sistema.',
            'doctor_id.required' => 'El ID del médico es obligatorio.',
            'doctor_id.exists' => 'El médico no existe en el sistema.',
            'appointment_time.required' => 'La hora de la cita es obligatoria.',
            'appointment_time.in' => 'La hora de la cita debe ser uno de los horarios permitidos (ej. 09:00, 09:30).',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $userId = $this->input('user_id');
            $doctorId = $this->input('doctor_id');
            $appointmentTime = $this->input('appointment_time');

            if (! $userId || ! $doctorId || ! $appointmentTime) {
                return;
            }

            $appointmentDate = Carbon::today()->format('Y-m-d').' '.$appointmentTime.':00';

            if (Appointment::where('user_id', $userId)
                ->whereDate('appointment_date', Carbon::today())
                ->exists()) {
                $validator->errors()->add('appointment_time', 'El paciente ya tiene una cita agendada para hoy.');
            }

            if (Appointment::where('doctor_id', $doctorId)
                ->where('appointment_date', $appointmentDate)
                ->exists()) {
                $validator->errors()->add('appointment_time', 'El médico ya tiene una cita asignada en ese horario.');
            }
        });
    }
}
