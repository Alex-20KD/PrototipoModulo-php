<?php

namespace App\Modules\Triage\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Modules\Triage\Models\Appointment;
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

    public function pdf(Appointment $appointment)
    {
        $appointment->load(['user', 'doctor', 'vitalSigns']);

        $pdf = Pdf::loadView('triage.pdf.formulario002', compact('appointment'));

        return $pdf->download('formulario002.pdf');
    }
}
