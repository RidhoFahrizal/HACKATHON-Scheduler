<?php

use App\Http\Controllers\AcademicSchedulerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AcademicSchedulerController::class, 'index'])->name('scheduler.index');
Route::post('/switch-role', [AcademicSchedulerController::class, 'switchRole'])->name('scheduler.switch-role');
