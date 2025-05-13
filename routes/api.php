<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\API\AuthController;

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

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware(['auth', 'role:ADMIN,SUPER_ADMIN,TUTOR_WORKER'])->prefix('tasks')->group(function () {
    Route::get('/', [TaskController::class, 'getTasks']);
    Route::get('/candidate/{id}', [TaskController::class, 'getCandidateInfo']);
    Route::get('/task-status/{id}', [TaskController::class, 'getTaskStatus']);
    Route::get('/task/{taskStatusId}', [TaskController::class, 'getTaskInfo']);
    Route::put('/status/{taskStatusId}', [TaskController::class, 'updateStatus']);
    Route::post('/report/{taskStatusId}', [TaskController::class, 'createReport']);
    Route::get('/statuses', [TaskController::class, 'getStatuses']);
});