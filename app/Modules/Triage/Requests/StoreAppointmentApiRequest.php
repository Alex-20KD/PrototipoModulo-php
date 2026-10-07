<?php

namespace App\Modules\Triage\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}
