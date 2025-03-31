<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CareerController;

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

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('auth');

Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/career', [CareerController::class, 'index'])->name('career');
Route::post('/career', [CareerController::class, 'submitApplication'])->name('career.submit');

Route::get('/profile', function () {
    return view('users.user-information');
})->name('profile');

Route::get('/task', function () {
    return view('users.task');
})->name('task');

Route::get('/chat', function () {
    return view('users.chat');
})->name('chat');

Route::get('/worker-chat', function () {
    return view('workers.chat');
})->name('worker-chat');

Route::get('/tasks', function () {
    return view('workers.tasks');
})->name('tasks');

Route::get('/meeting', function () {
    return view('workers.meeting');
})->name('meeting');

Route::get('/reset-password', function () {
    return view('auth.reset-password');
})->name('reset-password');