<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::get('/career', function () {
    return view('career');
})->name('career');

Route::get('/profile', function () {
    return view('users.user-information');
})->name('profile');

Route::get('/task', function () {
    return view('users.task');
})->name('task');

Route::get('/chat', function () {
    return view('users.chat');
})->name('chat');
