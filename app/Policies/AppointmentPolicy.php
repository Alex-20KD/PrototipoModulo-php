<?php

namespace App\Policies;

use App\Models\Staff;
use App\Modules\Triage\Models\Appointment;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(Staff $staff): bool
    {
        return true;
    }

    public function view(Staff $staff, Appointment $appointment): bool
    {
        if ($staff->isDoctor()) {
            return ! empty($staff->doctor_id) && ! empty($appointment->doctor_id) && (int) $staff->doctor_id === (int) $appointment->doctor_id;
        }

        return true;
    }

    public function history(Staff $staff): bool
    {
        return $staff->isDoctor();
    }

    public function downloadPdf(Staff $staff, Appointment $appointment): bool
    {
        if ($staff->isDoctor()) {
            return ! empty($staff->doctor_id) && ! empty($appointment->doctor_id) && (int) $staff->doctor_id === (int) $appointment->doctor_id;
        }

        return false;
    }
}
