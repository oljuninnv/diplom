<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


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

    public function login(Request $request){

        $values = $request->all();

        if (Auth::attempt(['email' => $values['email'], 'password' => $values['password']])) {
            Auth::user();
            Auth::guard('moonshine')->login(Auth::user());
            return redirect('/');
        }

        return back()->withErrors([
            'credentials' => 'Неверные учетные данные.',
        ])->withInput();
    
    }
}
