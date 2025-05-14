<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TaskApiController;
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

Route::middleware(['auth:api'])->prefix('tasks')->group(function () {
    Route::get('/', [TaskApiController::class, 'getTasks']);
    Route::get('/candidate/{id}', [TaskApiController::class, 'getCandidateInfo']);
    Route::get('/task-status/{id}', [TaskApiController::class, 'getTaskStatus']);
    Route::get('/task/{taskStatusId}', [TaskApiController::class, 'getTaskInfo']);
    Route::put('/status/{taskStatusId}', [TaskApiController::class, 'updateStatus']);
    Route::post('/report/{taskStatusId}', [TaskApiController::class, 'createReport']);
    Route::get('/statuses', [TaskApiController::class, 'getStatuses']);
});