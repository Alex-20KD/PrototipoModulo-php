<?php

use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'status'  => true,
        'message' => 'pong',
        'data'    => null,
    ], 200);
})->name('api.ping');
