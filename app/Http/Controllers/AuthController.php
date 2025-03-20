<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Enums\UserRoleEnum;

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
            \Log::info($user->role->name === UserRoleEnum::ADMIN->value);
            if ($user->role->name === UserRoleEnum::ADMIN->value) {
                Auth::guard('moonshine')->login($user);
            }
            return redirect('/');
        }

        return back()->withErrors([
            'credentials' => 'Неверные учетные данные.',
        ])->withInput();

    }
}
