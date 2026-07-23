<?php

namespace App\Modules\Triage\Controllers;

use App\Modules\Triage\Models\Appointment;
use App\Modules\Triage\Models\Prescription;
use App\Modules\Triage\Models\VitalSign;
use Carbon\Carbon;
use Illuminate\Routing\Controller;

class ReportsController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $dailyPatients = Appointment::query()
            ->selectRaw('DATE(appointment_date) as date, COUNT(*) as count')
            ->whereDate('appointment_date', '>=', $today->copy()->subDays(29))
            ->groupByRaw('DATE(appointment_date)')
            ->orderByRaw('DATE(appointment_date) ASC')
            ->get();

        $topDiagnoses = Appointment::query()
            ->selectRaw('cie10_code, cie10_description, COUNT(*) as count')
            ->whereNotNull('cie10_code')
            ->groupBy('cie10_code', 'cie10_description')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $prescriptionsCount = Prescription::count();
        $completedToday = Appointment::where('status', 'completed')
            ->whereDate('appointment_date', $today)
            ->count();
        $totalPatients = Appointment::distinct('user_id')->count('user_id');
        $pendingTriages = VitalSign::where('status', 'pending')->count();

        return view('triage.reports.index', compact(
            'dailyPatients',
            'topDiagnoses',
            'prescriptionsCount',
            'completedToday',
            'totalPatients',
            'pendingTriages',
            'today',
        ));
    }
}
