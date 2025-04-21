<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Requests\FirstLoginPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('moonshine')->logout();
        Auth::logout();
        
        return redirect()->route('home');
    }

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return back()
                ->withErrors(['credentials' => 'Неверные учетные данные.'])
                ->withInput();
        }

        /** @var User $user */
        $user = Auth::user();
        
        if (!$user instanceof User) {
            Auth::logout();
            return redirect()->route('home');
        }

        if (empty($user->date_of_auth)) {
            Auth::logout();
            return redirect()->route('restore-password.form', $user);
        }

        if (!$user->telegramUser) {
            return $this->redirectToTelegramLink($user);
        }

        $this->updateUserAuthDate($user);
        $this->authenticateInMoonshineIfAdmin($user);

        return redirect()->intended('/');
    }

    public function showRestorePasswordForm(User $user): View
    {
        return view('auth.restore-password', compact('user'));
    }

    public function restorePassword(FirstLoginPasswordRequest $request, User $user): RedirectResponse
    {
        $user->update([
            'password' => Hash::make($request->validated()['password']),
            'date_of_auth' => now(),
        ]);

        Auth::login($user);
        $this->authenticateInMoonshineIfAdmin($user);

        return redirect('/')->with('success', 'Пароль успешно изменён!');
    }

    protected function redirectToTelegramLink(User $user): RedirectResponse
    {
        $hash = sha1($user->id . config('app.key'));
        
        return redirect()->route('telegram.link', [
            'user_id' => $user->id,
            'hash' => $hash
        ]);
    }

    protected function updateUserAuthDate(User $user): void
    {
        $user->update(['date_of_auth' => now()]);
    }

    protected function authenticateInMoonshineIfAdmin(User $user): void
    {
        if ($user->isAdmin()) {
            Auth::guard('moonshine')->login($user);
        }
    }
}