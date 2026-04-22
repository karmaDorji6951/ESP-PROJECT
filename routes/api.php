<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Task notification and submission routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tasks/{task}/submit', [TaskController::class, 'submitTask'])->name('tasks.submit');
    Route::get('/tasks/{task}/submission-status', [TaskController::class, 'checkSubmissionStatus'])->name('tasks.submission-status');
    Route::post('/tasks/{task}/assign-notification', [TaskController::class, 'assignTaskWithNotification'])->name('tasks.assign-notification');
});

