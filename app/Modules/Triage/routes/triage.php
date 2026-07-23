<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Triage\Controllers\NursingController;
use App\Modules\Triage\Controllers\ReceptionController;
use App\Modules\Triage\Controllers\DoctorController;
use App\Modules\Triage\Controllers\ReportsController;

Route::prefix('triage')->group(function () {

    // Nursing (Enfermería - Triaje)
    Route::get('/nursing', [NursingController::class, 'index'])->name('triage.nursing.index');
    Route::post('/nursing/triage', [NursingController::class, 'store'])->name('triage.nursing.store');

    // Reception (Recepción - Citas)
    Route::get('/reception', [ReceptionController::class, 'index'])->name('triage.reception.index');
    Route::post('/reception/appointment', [ReceptionController::class, 'store'])->name('triage.reception.store');

    // Doctor
    Route::get('/doctor', [DoctorController::class, 'index'])->name('triage.doctor.index');
    Route::get('/doctor/pdf/{appointment}', [DoctorController::class, 'pdf'])->name('triage.doctor.pdf');
    Route::get('/doctor/cie10', [DoctorController::class, 'searchCie10'])->name('triage.doctor.cie10');
    Route::get('/doctor/attend/{appointment}', [DoctorController::class, 'attend'])->name('triage.doctor.attend');
    Route::post('/doctor/attend/{appointment}', [DoctorController::class, 'store'])->name('triage.doctor.attend.store');
    Route::get('/patients/{user_id}/history', [DoctorController::class, 'history'])->name('triage.patient.history');

    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('triage.reports.index');

});
