<?php

use App\Modules\Triage\Controllers\Api\NursingController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'status' => true,
        'message' => 'pong',
        'data' => null,
    ]);
});

Route::post('/triage/vital-signs', [NursingController::class, 'store']);
