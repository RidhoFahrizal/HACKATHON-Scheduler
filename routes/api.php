<?php

use App\Http\Controllers\SchedulingController;
use Illuminate\Support\Facades\Route;

Route::get('/scheduling/catalog', [SchedulingController::class, 'catalog']);
Route::post('/scheduling/evaluate', [SchedulingController::class, 'evaluate']);
Route::get('/scheduling/evaluate/stream', [SchedulingController::class, 'evaluateStream']);
