<?php

use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\ImportController;
use App\Http\Controllers\Api\V1\LecturerController;
use App\Http\Controllers\Api\V1\RescheduleRequestController;
use App\Http\Controllers\Api\V1\RoomController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\SubjectController;
use App\Http\Controllers\SchedulingController;
use Illuminate\Support\Facades\Route;

Route::get('/scheduling/catalog', [SchedulingController::class, 'catalog']);
Route::post('/scheduling/evaluate', [SchedulingController::class, 'evaluate']);
Route::get('/scheduling/evaluate/stream', [SchedulingController::class, 'evaluateStream']);

// The web group supplies the session that stores the role chosen in the role switcher.
Route::prefix('v1')->middleware('web')->group(function () {
    Route::apiResource('rooms', RoomController::class)->except('show');
    Route::apiResource('subjects', SubjectController::class)->except('show');
    Route::apiResource('lecturers', LecturerController::class)->except('show');
    Route::apiResource('students', StudentController::class)->except('show');

    Route::get('reschedule-requests', [RescheduleRequestController::class, 'index']);
    Route::post('reschedule-requests', [RescheduleRequestController::class, 'store']);
    Route::post('reschedule-requests/{rescheduleRequest}/approve', [RescheduleRequestController::class, 'approve']);
    Route::post('reschedule-requests/{rescheduleRequest}/reject', [RescheduleRequestController::class, 'reject']);

    Route::post('chat/message', [ChatController::class, 'message']);

    Route::post('import/{entity}', [ImportController::class, 'store'])
        ->whereIn('entity', ['rooms', 'subjects', 'lecturers', 'students', 'schedules']);
});
