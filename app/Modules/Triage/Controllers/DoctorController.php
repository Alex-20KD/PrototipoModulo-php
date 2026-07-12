<?php

namespace App\Modules\Triage\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Modules\Triage\Models\Appointment;
use App\Modules\Triage\Models\Cie10;
use App\Modules\Triage\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class DoctorController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['user', 'doctor', 'vitalSigns'])
            ->whereDate('appointment_date', Carbon::today())
            ->orderBy('appointment_date')
            ->paginate(10);

        return view('triage.doctor.index', compact('appointments'));
    }

    public function attend(Appointment $appointment)
    {
        $appointment->load(['user', 'doctor', 'vitalSigns']);

        return view('triage.doctor.attend', compact('appointment'));
    }

    public function store(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'anamnesis'       => 'required|string',
            'antecedentes'    => 'nullable|string',
            'cie10_code'      => 'required|string|max:10|exists:triage_cie10,code',
            'diagnosis_type'  => 'required|in:presuntivo,definitivo',
            'prescriptions'                => 'nullable|array',
            'prescriptions.*.generic_name' => 'required_with:prescriptions|string',
            'prescriptions.*.concentration'=> 'required_with:prescriptions|string',
            'prescriptions.*.form'         => 'required_with:prescriptions|string',
            'prescriptions.*.quantity'     => 'required_with:prescriptions|integer|min:1',
            'prescriptions.*.indications'  => 'required_with:prescriptions|string',
        ]);

        // Always re-fetch the description from the catalog — never trust client-side data
        $cie10 = Cie10::where('code', $validated['cie10_code'])->firstOrFail();

        $appointment->update([
            'anamnesis'        => $validated['anamnesis'],
            'antecedentes'     => $validated['antecedentes'] ?? null,
            'cie10_code'       => $cie10->code,
            'cie10_description'=> $cie10->description,
            'diagnosis_type'   => $validated['diagnosis_type'],
            'status'           => 'completed',
        ]);

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

    public function pdf(Appointment $appointment)
    {
        $appointment->load(['user', 'doctor', 'vitalSigns', 'prescriptions']);

        $pdf = Pdf::loadView('triage.pdf.formulario002', compact('appointment'));

        return $pdf->download('formulario002.pdf');
    }
}
