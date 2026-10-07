<?php

namespace App\Policies;

use App\Models\Staff;
use App\Modules\Triage\Models\Appointment;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(Staff $staff)
    {
        return true;
    }

    public function view(Staff $staff, Appointment $appointment)
    {
        if ($staff->isDoctor()) {
            return $staff->doctor_id === $appointment->doctor_id;
        }

        return true;
    }

    public function history(Staff $staff)
    {
        return $staff->isDoctor();
    }

    public function downloadPdf(Staff $staff, Appointment $appointment)
    {
        if ($staff->isDoctor()) {
            return $staff->doctor_id === $appointment->doctor_id;
        }

        return false;
    }
}
