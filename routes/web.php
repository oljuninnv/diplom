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
use App\Http\Controllers\TelegramLinkController;
use App\Http\Controllers\ApplicationController;

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
    Route::put('/profile/telegram/unlink', [ProfileController::class, 'unlinkTelegram'])->name('profile.telegram.unlink');
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
    });
});

Route::group(['middleware' => 'auth', 'role:ADMIN,SUPER_ADMIN'], function () {
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::post('/applications/{application}/approve', [ApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{application}/decline', [ApplicationController::class, 'decline'])->name('applications.decline');
    Route::post('/applications/{application}/under-consideration', [ApplicationController::class, 'underConsideration'])->name('applications.under_consideration');
    Route::post('/applications/{application}/assign-call', [ApplicationController::class, 'assignCall'])->name('applications.assign_call');
    Route::get('/applications/{application}/download', [ApplicationController::class, 'download'])
        ->name('applications.download');
});

Route::get('/departments/{department}/tasks', function (App\Models\Department $department) {
    return \App\Models\Task::whereHas('post', function ($query) use ($department) {
        $query->where('department_id', $department->id);
    })->select('id', 'title')->get();
});

Route::middleware(['auth', 'role:ADMIN,SUPER_ADMIN,TUTOR_WORKER'])->prefix('tasks')->group(function () {
    Route::get('/get_tasks', [TaskController::class, 'getTasks']);
    Route::get('/candidate/{id}', [TaskController::class, 'getCandidateInfo']);
    Route::get('/worker/{id}', [TaskController::class, 'getWorkerInfo']);
    Route::get('/tutor/{id}', [TaskController::class, 'getTutorInfo']);
    Route::get('/hr-manager/{id}', [TaskController::class, 'getHrManagerInfo']);
    Route::get('/task-status/{id}', [TaskController::class, 'getTaskStatus']);
    Route::get('/task/{taskStatusId}', [TaskController::class, 'getTaskInfo']);
    Route::put('/status/{taskStatusId}', [TaskController::class, 'updateStatus']);
    Route::post('/report/{taskStatusId}', [TaskController::class, 'createReport']);
    Route::get('/statuses', [TaskController::class, 'getStatuses']);
    Route::post('/adopted/{taskStatusId}', [TaskController::class, 'adopted']);
    Route::post('/failed/{taskStatusId}', [TaskController::class, 'failed']);
    Route::post('/technical_call/{taskStatusId}', [TaskController::class, 'technicalCall']);
    Route::post('/final_call/{taskStatusId}', [TaskController::class, 'finalCall']);
});

Route::post('/telegram-webhook', [MessageController::class, '__invoke']);

Route::get('/telegram/link/{user_id}/{hash}', [TelegramLinkController::class, 'showLinkForm'])
    ->name('telegram.link');

Route::get('/telegram/verify/{user_id}/{hash}', [TelegramLinkController::class, 'verifyLink'])
    ->name('telegram.verify');

Route::get('/telegram/skip/{user_id}/{hash}', [TelegramLinkController::class, 'skipLink'])
    ->name('telegram.skip');