<?php

namespace App\Modules\Triage\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transforma el recurso en un array con claves en snake_case
     * y fechas en formato ISO-8601 UTC.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'doctor_id' => $this->doctor_id,
            'vital_signs_id' => $this->vital_signs_id,
            'appointment_date' => $this->appointment_date?->toIso8601ZuluString(),
            'status' => $this->status,
            'anamnesis' => $this->anamnesis,
            'physical_exam' => $this->physical_exam,
            'antecedentes' => $this->antecedentes,
            'cie10_code' => $this->cie10_code,
            'cie10_description' => $this->cie10_description,
            'diagnosis_type' => $this->diagnosis_type,
            'diagnosis_type_label' => $this->diagnosis_type_label,
            'ant_hta' => $this->ant_hta,
            'ant_hta_years' => $this->ant_hta_years,
            'ant_hta_treatment' => $this->ant_hta_treatment,
            'ant_hta_medication' => $this->ant_hta_medication,
            'ant_dm' => $this->ant_dm,
            'ant_dm_years' => $this->ant_dm_years,
            'ant_dm_treatment' => $this->ant_dm_treatment,
            'ant_dm_medication' => $this->ant_dm_medication,
            'ant_chronic' => $this->ant_chronic,
            'ant_chronic_other' => $this->ant_chronic_other,
            'ant_observations' => $this->ant_observations,
            'vital_signs' => new VitalSignResource($this->whenLoaded('vitalSigns')),
            'created_at' => $this->created_at?->toIso8601ZuluString(),
            'updated_at' => $this->updated_at?->toIso8601ZuluString(),
        ];
    }
}
