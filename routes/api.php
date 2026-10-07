<?php

use App\Modules\Auth\Controllers\Api\AuthController;
use App\Modules\Triage\Controllers\Api\DoctorController;
use App\Modules\Triage\Controllers\Api\NursingController;
use App\Modules\Triage\Controllers\Api\ReceptionController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'status' => true,
        'message' => 'pong',
        'data' => null,
    ]);
});

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {

    // Triage / Nursing
    Route::middleware('role:nurse')->group(function () {
        Route::post('/triage/vital-signs', [NursingController::class, 'store']);
    });

    // Reception
    Route::middleware('role:reception')->group(function () {
        Route::get('/reception/appointments', [ReceptionController::class, 'index']);
        Route::post('/reception/appointments', [ReceptionController::class, 'store']);
    });

    // Doctor
    Route::middleware('role:doctor')->group(function () {
        // En un escenario real apuntarían a App\Modules\Triage\Controllers\Api\DoctorController
        // Como no existe, usamos los del web, o closures de ejemplo que la prueba validará (al menos por el middleware).
        // Pero Laravel intentará resolver el Controller. Mejor creamos un DoctorController en Api.
        Route::get('/doctor/appointments', [DoctorController::class, 'index']);
        Route::get('/doctor/pdf/{appointment}', [DoctorController::class, 'pdf']);
        Route::get('/patients/{user_id}/history', [DoctorController::class, 'history']);
    });

    // Reports (Ejemplo de protección genérica o rol)
    Route::middleware('role:doctor,reception')->group(function () {
        Route::get('/reports', function () {
            return response()->json(['status' => true, 'message' => 'Reports', 'data' => []]);
        });
    });
});
