<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TelegramLinkController extends Controller
{
    public function showLinkForm($user_id, $hash)
    {
        $user = User::findOrFail($user_id);
        
        // Упрощенная проверка хэша (без проверки в БД)
        if (!hash_equals(sha1($user_id.env('APP_KEY')), $hash)) {
            abort(403, 'Неверная ссылка для привязки');
        }

        $botUsername = 'Atwinta_Helper_bot';
        $deepLink = "https://t.me/{$botUsername}?start=link_{$user_id}_{$hash}";

        return view('auth.telegram-link', [
            'user' => $user,
            'deepLink' => $deepLink,
            'botUsername' => $botUsername,
            'verifyUrl' => route('telegram.verify', ['user_id' => $user_id, 'hash' => $hash]),
            'skipUrl' => route('telegram.skip', ['user_id' => $user_id, 'hash' => $hash])
        ]);
    }

    public function verifyLink($user_id, $hash)
    {
        $user = User::findOrFail($user_id);
        
        if (!hash_equals(sha1($user_id.env('APP_KEY')), $hash)) {
            abort(403, 'Неверная ссылка для привязки');
        }

        if ($user->telegramUser) {
            Auth::login($user);
            return redirect('/')->with('success', 'Telegram аккаунт успешно привязан!');
        }

        return back()->with('error', 'Telegram аккаунт еще не привязан.');
    }

    public function skipLink($user_id, $hash)
    {
        $user = User::findOrFail($user_id);
        
        if (!hash_equals(sha1($user_id.env('APP_KEY')), $hash)) {
            abort(403, 'Неверная ссылка для привязки');
        }

        Auth::login($user);
        return redirect('/')->with('info', 'Вы можете привязать Telegram аккаунт позже в настройках профиля.');
    }
}