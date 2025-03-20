<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        
        Auth::logout();

        return redirect()->route('home');
    }

    public function showLoginForm()
    {
        return view('auth/restore-password'); 
    }

    public function login(Request $request){

        $values = $request->all();

        \Log::info($values);


        if (Auth::attempt(['email' => $values['email'], 'password' => $values['password']])) {
            $user = Auth::user();

            if ($user->banned == 1) {
                Auth::logout();
                return back()->withErrors([
                    'credentials' => 'Ваш профиль заблокирован.',
                ])->withInput();
            }

            return redirect('/');
        }

        return back()->withErrors([
            'credentials' => 'Неверные учетные данные.',
        ])->withInput();
    
    }
}
