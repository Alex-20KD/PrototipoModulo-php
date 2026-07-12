<?php

namespace App\Modules\Triage\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Modules\Triage\Models\VitalSign;

class NursingController extends Controller
{
    public function index(Request $request)
    {
        $patient = null;
        $userModel = config('triage.user_model');
        $idColumn = config('triage.user_id_column');

        if ($request->has('cedula') && $request->cedula !== '') {
            $patient = $userModel::where($idColumn, $request->cedula)->first();
        }

        return view('triage.nursing.index', compact('patient'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'blood_pressure' => ['required', 'string', 'regex:/^\d{2,3}\/\d{2,3}$/'],
            'heart_rate' => 'required|integer|min:30|max:250',
            'weight_kg' => 'required|numeric|min:1|max:300',
            'height_cm' => 'required|numeric|min:30|max:250',
            'temperature' => 'required|numeric|min:34|max:42',
            'respiratory_rate' => 'required|integer|min:8|max:40',
            'reason_for_consultation' => 'required|string',
        ], [
            'blood_pressure.required'  => 'La presión arterial es obligatoria.',
            'blood_pressure.regex'     => 'Formato inválido. Use el formato 120/80.',
            'heart_rate.min'           => 'La frecuencia cardíaca mínima es 30 lpm.',
            'heart_rate.max'           => 'La frecuencia cardíaca máxima es 250 lpm.',
            'weight_kg.min'            => 'El peso mínimo es 1 kg.',
            'weight_kg.max'            => 'El peso máximo es 300 kg.',
            'height_cm.min'            => 'La talla mínima es 30 cm.',
            'height_cm.max'            => 'La talla máxima es 250 cm.',
            'temperature.min'          => 'La temperatura mínima registrable es 34°C.',
            'temperature.max'          => 'La temperatura máxima registrable es 42°C.',
            'respiratory_rate.min'     => 'La frecuencia respiratoria mínima es 8 rpm.',
            'respiratory_rate.max'     => 'La frecuencia respiratoria máxima es 40 rpm.',
        ]);

        $parts = explode('/', $validated['blood_pressure']);
        $systolic = (int)$parts[0];
        $diastolic = (int)$parts[1];

        if ($systolic < 60 || $systolic > 250) {
            return back()->withErrors([
                'blood_pressure' => 'La presión sistólica debe estar entre 60 y 250 mmHg.'
            ])->withInput();
        }
        if ($diastolic < 40 || $diastolic > 150) {
            return back()->withErrors([
                'blood_pressure' => 'La presión diastólica debe estar entre 40 y 150 mmHg.'
            ])->withInput();
        }
        if ($systolic <= $diastolic) {
            return back()->withErrors([
                'blood_pressure' => 'La presión sistólica debe ser mayor que la diastólica.'
            ])->withInput();
        }

        VitalSign::create([
            'user_id' => $request->user_id,
            'blood_pressure' => $request->blood_pressure,
            'heart_rate' => $request->heart_rate,
            'weight_kg' => $request->weight_kg,
            'height_cm' => $request->height_cm,
            'temperature' => $request->temperature,
            'respiratory_rate' => $request->respiratory_rate,
            'reason_for_consultation' => $request->reason_for_consultation,
            'status' => 'pending',
        ]);

        return redirect()->route('triage.nursing.index')->with('success', '¡Triaje guardado exitosamente como PENDIENTE!');
    }
}
