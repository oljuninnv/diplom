<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function showProfile()
    {
        return view('users.user-information');
    }

    public function unlinkTelegram(Request $request)
    {
        $user = Auth::user();

        if (!$user->telegramUser) {
            return response()->json([
                'success' => false,
                'message' => 'Telegram аккаунт не привязан'
            ], 400);
        }

        try {
            $telegramUser = $user->telegramUser;
            $user->update(['telegram_user_id' => null]);
            $telegramUser->delete();
            return response()->json([
                'success' => true,
                'message' => 'Telegram аккаунт успешно отвязан'
            ]);
        } catch (\Exception $e) {
            \Log::error('Ошибка отвязки Telegram: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при отвязке Telegram'
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Валидация данных (только для аватара, так как email и телефон теперь не редактируются)
        $validator = Validator::make($request->all(), [
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Обработка аватара
            if ($request->hasFile('avatar')) {
                // Удаляем старый аватар
                if ($user->avatar) {
                    Storage::delete($user->avatar);
                }

                // Сохраняем новый аватар
                $path = $request->file('avatar')->store('public/moonshine_users');
                $user->avatar = str_replace('public/', '', $path);
            }

            $user->save();

            return redirect()->route('profile')
                ->with('success', 'Профиль успешно обновлен!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Произошла ошибка при обновлении профиля: ' . $e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // Валидация данных
        $request->validate([
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'
            ],
        ], [
            'new_password.required' => 'Поле нового пароля обязательно для заполнения',
            'new_password.min' => 'Пароль должен содержать минимум 8 символов',
            'new_password.confirmed' => 'Пароли не совпадают',
            'new_password.regex' => 'Пароль должен содержать хотя бы одну заглавную букву, одну строчную букву и одну цифру'
        ]);

        try {
            // Обновляем пароль
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            // Опционально: выход из всех устройств кроме текущего
            Auth::logoutOtherDevices($request->new_password);

            return redirect()->route('profile')
                ->with('success', 'Пароль успешно изменен! Теперь вы можете войти с новым паролем.');

        } catch (\Exception $e) {
            \Log::error('Ошибка при смене пароля: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'error' => $e
            ]);

            return back()
                ->with('error', 'Произошла ошибка при изменении пароля. Пожалуйста, попробуйте позже.')
                ->withInput();
        }
    }
}