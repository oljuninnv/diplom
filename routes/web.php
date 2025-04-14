<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\RestorePasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CandidateTaskController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\WorkerChatController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\MessageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Аутентификация
Route::middleware(['guest'])->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('auth');
    
    Route::get('/auth/restore-password/{user}', [AuthController::class, 'showRestorePasswordForm'])
        ->name('restore-password.form');
    Route::post('/auth/restore-password/{user}', [AuthController::class, 'restorePassword'])
        ->name('restore-password');
    
    Route::get('/auth/reset-password', [RestorePasswordController::class, 'showResetPasswordForm'])
        ->name('reset-password.form');
    Route::post('/auth/reset-password', [RestorePasswordController::class, 'reset_password'])
        ->name('reset-password');
    
    Route::get('/auth/reset-password/confirm', [RestorePasswordController::class, 'showResetPasswordConfirmForm'])
        ->name('auth.restore-password-confirm');
    
    Route::post('/auth/reset-password/update', [RestorePasswordController::class, 'resetPassword'])
        ->name('password.update');
    
    Route::get('reset_password/confirm', [RestorePasswordController::class, 'showResetPasswordConfirmForm'])
        ->name('resetPasswordConfirm_form');
    Route::post('reset_password/confirm', [RestorePasswordController::class, 'resetPassword'])
        ->name('reset_password_confirm');
});

// Выход из системы (только для авторизованных)
Route::middleware(['auth'])->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
});

// Информационная страница (доступна без авторизации)
Route::get('/career', [CareerController::class, 'index'])->name('career');
Route::post('/career', [CareerController::class, 'submitApplication'])->name('career.submit');

// Защищенные маршруты (требуют авторизации)
Route::middleware(['auth'])->group(function () {
    // Профиль
    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    
    // Задания
    Route::middleware(['role:USER'])->group(function () {
        Route::get('/task', [CandidateTaskController::class, 'show'])->name('task');
        Route::post('/task/submit', [CandidateTaskController::class, 'submit'])->name('task.submit');
    });
    
    // Чат кандидата
    Route::middleware(['role:USER'])->group(function () {
        Route::get('/chat/{interlocutor?}', [ChatController::class, 'index'])->name('chat');
        Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
        Route::delete('/chat/delete/{message}', [ChatController::class, 'deleteMessage'])->name('chat.delete');
    });
    
    // Чат сотрудников
    Route::middleware(['role:TUTOR_WORKER,ADMIN,SUPER_ADMIN'])->group(function () {
        Route::get('/worker-chat/{interlocutor?}', [WorkerChatController::class, 'index'])->name('worker-chat');
        Route::post('/worker-chat/send', [WorkerChatController::class, 'sendMessage'])->name('worker-chat.send');
        Route::delete('/worker-chat/delete/{message}', [WorkerChatController::class, 'deleteMessage'])->name('worker-chat.delete');
    });
    
    // Список задач
    Route::middleware(['role:TUTOR_WORKER,ADMIN,SUPER_ADMIN'])->group(function () {
        Route::get('/tasks', [TaskController::class, 'index'])->name('tasks');
    });
    
    // Созвоны
    Route::middleware(['role:TUTOR_WORKER,ADMIN,SUPER_ADMIN'])->group(function () {
        Route::resource('meetings', MeetingController::class);
        Route::get('/users/{user}', [MeetingController::class, 'getUserData']);
        Route::get('/meetings-all', [MeetingController::class, 'getAllCalls']);
    });
});

// API задачи (требуют авторизации)
Route::middleware(['auth', 'role:ADMIN,SUPER_ADMIN,TUTOR_WORKER'])->prefix('api/tasks')->group(function () {
    Route::get('/', [TaskController::class, 'getTasks']);
    Route::get('/candidate/{id}', [TaskController::class, 'getCandidateInfo']);
    Route::get('/task-status/{id}', [TaskController::class, 'getTaskStatus']);
    Route::get('/task/{taskStatusId}', [TaskController::class, 'getTaskInfo']);
    Route::put('/status/{taskStatusId}', [TaskController::class, 'updateStatus']);
    Route::post('/report/{taskStatusId}', [TaskController::class, 'createReport']);
    Route::get('/statuses', [TaskController::class, 'getStatuses']);
});

Route::post('/telegram-webhook', [MessageController::class, '__invoke']);