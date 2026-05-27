<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('triage.nursing.index');
});

// Load Triage module routes
require base_path('app/Modules/Triage/routes/triage.php');
