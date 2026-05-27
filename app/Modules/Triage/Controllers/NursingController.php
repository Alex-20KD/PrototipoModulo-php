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
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'blood_pressure' => 'required|string',
            'heart_rate' => 'required|integer',
            'weight_kg' => 'required|numeric',
            'height_cm' => 'required|numeric',
            'temperature' => 'required|numeric',
            'reason_for_consultation' => 'required|string',
        ]);

        VitalSign::create([
            'user_id' => $request->user_id,
            'blood_pressure' => $request->blood_pressure,
            'heart_rate' => $request->heart_rate,
            'weight_kg' => $request->weight_kg,
            'height_cm' => $request->height_cm,
            'temperature' => $request->temperature,
            'reason_for_consultation' => $request->reason_for_consultation,
            'status' => 'pending',
        ]);

        return redirect()->route('triage.nursing.index')->with('success', '¡Triaje guardado exitosamente como PENDIENTE!');
    }
}
