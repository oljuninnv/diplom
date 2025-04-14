<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\TelegramUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\UserRoleEnum;
use App\Http\Requests\RestorePasswordRequest;

class AuthController extends Controller
{
    public function logout(Request $request)
    {
        Auth::guard('moonshine')->logout();

        Auth::logout();

        return redirect()->route('home');
    }

    public function showLoginForm()
    {
        return view('auth/login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (empty($user->date_of_auth)) {
                Auth::logout();
                return redirect()->route('restore-password.form', $user);
            }

            if (!$user->telegramUser) {
                $hash = sha1($user->id . env('APP_KEY'));
                return redirect()->route('telegram.link', [
                    'user_id' => $user->id,
                    'hash' => $hash
                ]);
            }

            $user->update(['date_of_auth' => now()]);

            if ($user->isAdmin()) {
                Auth::guard('moonshine')->login($user);
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'credentials' => 'Неверные учетные данные.',
        ])->withInput();
    }

    public function showRestorePasswordForm(User $user)
    {
        return view('auth.restore-password', ['user' => $user]);
    }

    public function restorePassword(RestorePasswordRequest $request, User $user)
    {
        $user->update([
            'password' => Hash::make($request->password),
            'date_of_auth' => now(),
        ]);

        Auth::login($user);

        if ($user->role->name === UserRoleEnum::ADMIN->value) {
            Auth::guard('moonshine')->login($user);
        }

        return redirect('/')->with('success', 'Пароль успешно изменен!');
    }
}
