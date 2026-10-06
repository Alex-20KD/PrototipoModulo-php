<?php

namespace App\Modules\Triage\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VitalSignResource extends JsonResource
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
            'blood_pressure' => $this->blood_pressure,
            'heart_rate' => $this->heart_rate,
            'weight_kg' => (float) $this->weight_kg,
            'height_cm' => (float) $this->height_cm,
            'temperature' => (float) $this->temperature,
            'respiratory_rate' => $this->respiratory_rate,
            'reason_for_consultation' => $this->reason_for_consultation,
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601ZuluString(),
            'updated_at' => $this->updated_at?->toIso8601ZuluString(),
        ];
    }
}
