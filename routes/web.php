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


// Вход в систему
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('auth');

Route::get('logout', [AuthController::class, 'logout'])->name('logout');

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

// Обработка нового пароля
Route::post('/auth/reset-password/update', [RestorePasswordController::class, 'resetPassword'])
    ->name('password.update');

Route::get('reset_password/confirm', [RestorePasswordController::class, 'showResetPasswordConfirmForm'])->name('resetPasswordConfirm_form');
Route::post('reset_password/confirm', [RestorePasswordController::class, 'resetPassword'])->name('reset_password_confirm');

// Информационная страница
Route::get('/career', [CareerController::class, 'index'])->name('career');
Route::post('/career', [CareerController::class, 'submitApplication'])->name('career.submit');

// Страница профиля
Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

// Задание
Route::middleware(['auth'])->group(function () {
    Route::get('/task', [CandidateTaskController::class, 'show'])->name('task');
    Route::post('/task/submit', [CandidateTaskController::class, 'submit'])->name('task.submit');
});

// Чат кандидата
Route::middleware(['auth'])->group(function () {
    Route::get('/chat/{interlocutor?}', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::delete('/chat/delete/{message}', [ChatController::class, 'deleteMessage'])->name('chat.delete');
});

// Чат сотрудников
Route::middleware(['auth'])->group(function() {
    Route::get('/worker-chat/{interlocutor?}', [WorkerChatController::class, 'index'])->name('worker-chat');
    Route::post('/worker-chat/send', [WorkerChatController::class, 'sendMessage'])->name('worker-chat.send');
    Route::delete('/worker-chat/delete/{message}', [WorkerChatController::class, 'deleteMessage'])->name('worker-chat.delete');
});

// Список задач
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks');
Route::prefix('api/tasks')->group(function () {
    Route::get('/', [TaskController::class, 'getTasks']);
    Route::get('/candidate/{id}', [TaskController::class, 'getCandidateInfo']);
    Route::get('task-status/{id}', [TaskController::class, 'getTaskStatus']);
    Route::get('/task/{taskStatusId}', [TaskController::class, 'getTaskInfo']); // Изменено
    Route::put('/status/{taskStatusId}', [TaskController::class, 'updateStatus']); // Изменено
    Route::post('/report/{taskStatusId}', [TaskController::class, 'createReport']); // Изменено
    Route::get('/statuses', [TaskController::class, 'getStatuses']);
});

// Созвоны
Route::get('/meeting', function () {
    return view('workers.meeting');
})->name('meeting');
