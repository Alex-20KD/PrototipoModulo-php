<?php

namespace App\Modules\Triage\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use App\Modules\Triage\Models\Appointment;
use App\Modules\Triage\Models\Cie10;
use App\Modules\Triage\Models\Prescription;
use App\Modules\Triage\Models\AppointmentDiagnosis;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->input('fecha', Carbon::today()->toDateString());

        $appointments = Appointment::with(['user', 'doctor', 'vitalSigns'])
            ->whereDate('appointment_date', $fecha)
            ->orderBy('appointment_date')
            ->paginate(10)
            ->withQueryString();

        return view('triage.doctor.index', compact('fecha', 'appointments'));
    }

    public function history($user_id)
    {
        $patient = \App\Models\User::findOrFail($user_id);

        $appointments = Appointment::with(['doctor', 'vitalSigns', 'prescriptions', 'diagnoses'])
            ->where('user_id', $user_id)
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('triage.patients.history', compact('patient', 'appointments'));
    }

    public function attend(Appointment $appointment)
    {
        $appointment->load(['user', 'doctor', 'vitalSigns']);

        return view('triage.doctor.attend', compact('appointment'));
    }

    public function store(Request $request, Appointment $appointment)
    {
        // Custom rule: reject first-person phrasing (MSP Ecuador clinical standard)
        Validator::extend('no_primera_persona', function ($attribute, $value, $parameters, $validator) {
            $firstPersonPatterns = [
                '/^\s*(yo\s)/i',
                '/^\s*tengo\s/i',
                '/^\s*me\s+(duele|duelen|siento|encuentro|molesta|molestan)\s/i',
                '/^\s*siento\s/i',
                '/^\s*sufro\s/i',
                '/^\s*estoy\s/i',
                '/\btengo\b/i',
                '/\bme duele\b/i',
                '/\bme siento\b/i',
                '/\bsufro de\b/i',
                '/\bmi enfermedad\b/i',
                '/\byo tengo\b/i',
            ];
            foreach ($firstPersonPatterns as $pattern) {
                if (preg_match($pattern, $value)) {
                    return false;
                }
            }
            return true;
        });

        Validator::replacer('no_primera_persona', function ($message, $attribute, $rule, $parameters) {
            return 'El motivo de consulta debe redactarse en tercera persona (ej: "paciente refiere dolor de cabeza") o como síntoma (ej: "cefalea intensa"). No use primera persona (tengo, me duele, siento, etc.).';
        });

        $validated = $request->validate([
            'anamnesis'       => 'required|string|min:10|no_primera_persona',
            'physical_exam'   => 'nullable|string|max:2000',
            'diagnoses'                     => 'required|array|min:1',
            'diagnoses.*.cie10_code'        => 'required|string|exists:triage_cie10,code',
            'diagnoses.*.diagnosis_type'    => 'required|in:presuntivo_ingreso,definitivo_ingreso,presuntivo_alta,definitivo_alta',
            'diagnoses.*.is_primary'        => 'boolean',
            // Structured antecedentes
            'ant_hta'              => 'boolean',
            'ant_hta_years'        => 'nullable|integer|min:1|max:100',
            'ant_hta_treatment'    => 'boolean',
            'ant_hta_medication'   => 'nullable|string|max:200',
            'ant_dm'               => 'boolean',
            'ant_dm_years'         => 'nullable|integer|min:1|max:100',
            'ant_dm_treatment'     => 'boolean',
            'ant_dm_medication'    => 'nullable|string|max:200',
            'ant_chronic'          => 'nullable|array',
            'ant_chronic.*'        => 'in:tiroides,vih,ets,psiquiatrica,cancer,cardiopatia,otra',
            'ant_chronic_other'    => 'nullable|string|max:300',
            'ant_observations'     => 'nullable|string',
            // Prescriptions
            'prescriptions'                => 'nullable|array',
            'prescriptions.*.generic_name' => 'required_with:prescriptions|string',
            'prescriptions.*.concentration'=> 'required_with:prescriptions|string',
            'prescriptions.*.form'         => 'required_with:prescriptions|string',
            'prescriptions.*.quantity'     => 'required_with:prescriptions|integer|min:1',
            'prescriptions.*.indications'  => 'required_with:prescriptions|string',
        ]);

        // Always re-fetch the description from the catalog — never trust client-side data
        $primaryDiagnosis = $validated['diagnoses'][0];
        $primaryCie10 = Cie10::where('code', $primaryDiagnosis['cie10_code'])->firstOrFail();

        $htaActive = $request->boolean('ant_hta');
        $dmActive  = $request->boolean('ant_dm');
        $chronicList = $validated['ant_chronic'] ?? [];

        $appointment->update([
            'anamnesis'         => $validated['anamnesis'],
            'physical_exam'     => $validated['physical_exam'] ?? null,
            'cie10_code'        => $primaryCie10->code,
            'cie10_description' => $primaryCie10->description,
            'diagnosis_type'    => $primaryDiagnosis['diagnosis_type'],
            'status'            => 'completed',
            // HTA — clear sub-fields when unchecked
            'ant_hta'           => $htaActive,
            'ant_hta_years'     => $htaActive ? $validated['ant_hta_years'] : null,
            'ant_hta_treatment' => $htaActive ? $request->boolean('ant_hta_treatment') : false,
            'ant_hta_medication'=> $htaActive && $request->boolean('ant_hta_treatment') ? $validated['ant_hta_medication'] : null,
            // DM — same conditional logic
            'ant_dm'            => $dmActive,
            'ant_dm_years'      => $dmActive ? $validated['ant_dm_years'] : null,
            'ant_dm_treatment'  => $dmActive ? $request->boolean('ant_dm_treatment') : false,
            'ant_dm_medication' => $dmActive && $request->boolean('ant_dm_treatment') ? $validated['ant_dm_medication'] : null,
            // Chronic diseases
            'ant_chronic'       => $chronicList,
            'ant_chronic_other' => in_array('otra', $chronicList) ? $validated['ant_chronic_other'] : null,
            'ant_observations'  => $validated['ant_observations'] ?? null,
        ]);

        foreach ($validated['diagnoses'] as $i => $diag) {
            $cie10 = Cie10::where('code', $diag['cie10_code'])->first();

            AppointmentDiagnosis::create([
                'appointment_id' => $appointment->id,
                'cie10_code' => $diag['cie10_code'],
                'cie10_description' => $cie10?->description ?? '',
                'diagnosis_type' => $diag['diagnosis_type'],
                'is_primary' => $i === 0,
            ]);
        }

        // Create prescriptions if provided
        if (!empty($validated['prescriptions'])) {
            foreach ($validated['prescriptions'] as $rx) {
                Prescription::create([
                    'appointment_id' => $appointment->id,
                    'generic_name'   => $rx['generic_name'],
                    'concentration'  => $rx['concentration'],
                    'form'           => $rx['form'],
                    'quantity'       => $rx['quantity'],
                    'indications'    => $rx['indications'],
                ]);
            }
        }

        return redirect()->route('triage.doctor.index')
            ->with('success', '¡Consulta guardada exitosamente! La cita ha sido finalizada.');
    }

    public function searchCie10(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $query = $request->input('q');

        $results = Cie10::where('description', 'LIKE', "%{$query}%")
            ->orWhere('code', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get(['code', 'description']);

        return response()->json($results);
    }

    public function searchMedications(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2',
        ]);

        $query = $validated['q'];

        $medications = \Illuminate\Support\Facades\DB::table('triage_medications')
            ->where('generic_name', 'LIKE', "%{$query}%")
            ->orWhere('concentration', 'LIKE', "%{$query}%")
            ->orWhere('form', 'LIKE', "%{$query}%")
            ->orderBy('generic_name')
            ->limit(10)
            ->get(['id', 'generic_name', 'concentration', 'form', 'route', 'controlled'])
            ->map(fn ($medication) => [
                'id' => $medication->id,
                'generic_name' => $medication->generic_name,
                'concentration' => $medication->concentration,
                'form' => $medication->form,
                'route' => $medication->route,
                'controlled' => (bool) $medication->controlled,
                'controlled_warning' => (bool) $medication->controlled,
            ]);

        return response()->json($medications);
    }

    public function pdf(Appointment $appointment)
    {
        $appointment->load(['user', 'doctor', 'vitalSigns', 'prescriptions', 'diagnoses']);

        $pdf = Pdf::loadView('triage.pdf.formulario002', compact('appointment'));

        return $pdf->download('formulario002.pdf');
    }
}
