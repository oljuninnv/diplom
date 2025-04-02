<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordReset;
use App\Http\Requests\RestorePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RestorePasswordController extends Controller
{
    /**
     * Показывает форму запроса сброса пароля
     */
    public function showResetPasswordForm()
    {
        return view('auth.reset-password');
    }

    /**
     * Обрабатывает запрос на сброс пароля
     */
    public function reset_password(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Поле email обязательно для заполнения.',
            'email.email' => 'Введите корректный адрес электронной почты.',
            'email.exists' => 'Пользователь с таким email не найден.',
        ]);

        try {
            $user = User::where('email', $request->email)->firstOrFail();
            $token = Str::random(40);

            PasswordReset::updateOrCreate(
                ['email' => $request->email],
                ['token' => $token, 'created_at' => now()]
            );

            try {
                Mail::send('emails.orderMail', [
                    'resetUrl' => route('auth.restore-password-confirm', ['token' => $token, 'email' => $user->email])
                ], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Сброс пароля');
                });

                return back()->with('status', 'Ссылка для сброса пароля отправлена на вашу почту!');

            } catch (\Exception $mailException) {
                return back()->withErrors(['error' => 'Не удалось отправить письмо'])
                    ->with('mail_error', $mailException->getMessage());
            }

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Произошла ошибка: ' . $e->getMessage()]);
        }
    }

    /**
     * Показывает форму для ввода нового пароля
     */
    public function showResetPasswordConfirmForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        // Валидация токена
        $resetRecord = PasswordReset::where('email', $email)->first();

        if (!$resetRecord) {
            return redirect()->route('reset-password.form')
                ->withErrors(['error' => 'Недействительная или просроченная ссылка.']);
        }

        // Добавим проверку токена
        if ($resetRecord->token !== $token) {
            return redirect()->route('reset-password.form')
                ->withErrors(['error' => 'Неверный токен сброса пароля.']);
        }

        return view('auth.restorePasswordConfirmPage', [
            'token' => $token,
            'email' => $email
        ]);
    }

    public function resetPassword(RestorePasswordRequest $request)
    {
        try {
            $validated = $request->validated();

            // Проверяем токен
            $resetRecord = PasswordReset::where('email', $validated['email'])
                ->where('token', $validated['token'])
                ->first();

            if (!$resetRecord) {
                return back()->withErrors(['error' => 'Недействительная или просроченная ссылка.']);
            }

            // Обновляем пароль пользователя
            $user = User::where('email', $validated['email'])->firstOrFail();
            $user->update([
                'password' => Hash::make($validated['password']),
                'date_of_auth' => now()
            ]);

            // Удаляем использованный токен
            PasswordReset::where('email', $user->email)->delete();

            Log::info("Password successfully reset for user {$user->email}");

            return redirect()->route('login')
                ->with('success', 'Пароль успешно изменен! Теперь вы можете войти.');

        } catch (\Exception $e) {
            Log::error("Password reset confirmation error: " . $e->getMessage());
            return back()->withErrors(['error' => 'Произошла ошибка при смене пароля: ' . $e->getMessage()]);
        }
    }
}