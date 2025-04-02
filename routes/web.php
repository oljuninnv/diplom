<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\RestorePasswordController;

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
Route::get('/profile', function () {
    return view('users.user-information');
})->name('profile');

// Задание
Route::get('/task', function () {
    return view('users.task');
})->name('task');

// Чат кандидата
Route::get('/chat', function () {
    return view('users.chat');
})->name('chat');

// Чат сотрудников
Route::get('/worker-chat', function () {
    return view('workers.chat');
})->name('worker-chat');

// Список задач
Route::get('/tasks', function () {
    return view('workers.tasks');
})->name('tasks');

// Созвоны
Route::get('/meeting', function () {
    return view('workers.meeting');
})->name('meeting');
