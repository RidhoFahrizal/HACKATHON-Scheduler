<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SchedulingController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/api/scheduling/evaluate', [SchedulingController::class, 'evaluate']);
Route::get('/api/scheduling/evaluate/stream', [SchedulingController::class, 'evaluateStream']);
