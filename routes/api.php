<?php

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

Route::post('/triage/vital-signs', [NursingController::class, 'store']);

Route::get('/reception/appointments', [ReceptionController::class, 'index']);
Route::post('/reception/appointments', [ReceptionController::class, 'store']);

use App\Modules\Auth\Controllers\Api\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});
