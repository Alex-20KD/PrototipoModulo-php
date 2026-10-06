<?php

namespace App\Modules\Triage\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreVitalSignApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'blood_pressure' => ['required', 'string', 'regex:/^\d{2,3}\/\d{2,3}$/'],
            'heart_rate' => ['required', 'integer', 'min:30', 'max:250'],
            'weight_kg' => ['required', 'numeric', 'min:1', 'max:300'],
            'height_cm' => ['required', 'numeric', 'min:30', 'max:250'],
            'temperature' => ['required', 'numeric', 'min:34', 'max:42'],
            'respiratory_rate' => ['required', 'integer', 'min:8', 'max:40'],
            'reason_for_consultation' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El ID del paciente es obligatorio.',
            'user_id.exists' => 'El paciente no existe en el sistema.',
            'blood_pressure.required' => 'La presión arterial es obligatoria.',
            'blood_pressure.regex' => 'Formato inválido. Use el formato 120/80.',
            'heart_rate.min' => 'La frecuencia cardíaca mínima es 30 lpm.',
            'heart_rate.max' => 'La frecuencia cardíaca máxima es 250 lpm.',
            'weight_kg.min' => 'El peso mínimo es 1 kg.',
            'weight_kg.max' => 'El peso máximo es 300 kg.',
            'height_cm.min' => 'La talla mínima es 30 cm.',
            'height_cm.max' => 'La talla máxima es 250 cm.',
            'temperature.min' => 'La temperatura mínima registrable es 34°C.',
            'temperature.max' => 'La temperatura máxima registrable es 42°C.',
            'respiratory_rate.min' => 'La frecuencia respiratoria mínima es 8 rpm.',
            'respiratory_rate.max' => 'La frecuencia respiratoria máxima es 40 rpm.',
        ];
    }

    /**
     * Validaciones adicionales de rangos fisiológicos que no pueden expresarse
     * con reglas simples (sistólica vs diastólica, coherencia de valores).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $bp = $this->input('blood_pressure');

            if (! $bp || ! preg_match('/^\d{2,3}\/\d{2,3}$/', $bp)) {
                return; // ya fue atrapado por la regla regex
            }

            [$systolic, $diastolic] = array_map('intval', explode('/', $bp));

            if ($systolic < 60 || $systolic > 250) {
                $validator->errors()->add('blood_pressure', 'La presión sistólica debe estar entre 60 y 250 mmHg.');
            } elseif ($diastolic < 40 || $diastolic > 150) {
                $validator->errors()->add('blood_pressure', 'La presión diastólica debe estar entre 40 y 150 mmHg.');
            } elseif ($systolic <= $diastolic) {
                $validator->errors()->add('blood_pressure', 'La presión sistólica debe ser mayor que la diastólica.');
            }
        });
    }
}
