<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        $values = $request->all();

        if (Auth::attempt(['email' => $values['email'], 'password' => $values['password']])) {
            $user = Auth::user();

            // Проверяем наличие date_of_auth
            if (empty($user->date_of_auth)) {
                Auth::logout(); // Выходим, так как нужно сменить пароль
                return redirect()->route('restore-password.form', $user);
            }

            // Обновляем дату аутентификации на текущую
            $user->date_of_auth = now();
            $user->save();

            if ($user->role->name === UserRoleEnum::ADMIN->value) {
                Auth::guard('moonshine')->login($user);
            }

            return redirect('/');
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
