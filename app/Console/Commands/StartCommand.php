<?php

namespace App\Console\Commands;

use Telegram\Bot\Commands\Command;
use App\Models\User;
use App\Models\TelegramUser;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Objects\Message;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Обработчик команды /start для привязки аккаунта';

    public function handle()
    {
        /** @var Update $update */
        $update = $this->getUpdate();
        $message = $update->getMessage();
        
        try {
            Log::debug('Incoming /start command', [
                'message' => $message->toArray()
            ]);

            $text = trim($message->getText());
            
            if (preg_match('/^\/start link_(\d+)_([a-f0-9]+)$/i', $text, $matches)) {
                $this->processAccountLinking((int)$matches[1], $matches[2], $message);
            } else {
                $this->sendWelcomeMessage($message);
            }
            
        } catch (\Throwable $e) {
            Log::error('StartCommand error: '.$e->getMessage(), [
                'exception' => $e,
                'update' => $update->toArray()
            ]);
            
            $this->replyWithMessage([
                'text' => '⚠️ Произошла техническая ошибка. Пожалуйста, попробуйте позже.',
                'parse_mode' => 'HTML'
            ]);
        }
    }

    protected function processAccountLinking(int $userId, string $hash, $message)
    {
        Log::debug('Processing account linking', [
            'user_id' => $userId,
            'hash' => $hash
        ]);

        // Проверка хэша
        $expectedHash = sha1($userId.env('APP_KEY'));
        if (!hash_equals($expectedHash, $hash)) {
            Log::warning('Invalid hash for user linking', [
                'user_id' => $userId,
                'received_hash' => $hash,
                'expected_hash' => $expectedHash
            ]);
            
            return $this->replyWithMessage([
                'text' => '❌ Недействительная ссылка привязки. Пожалуйста, получите новую ссылку на сайте.',
                'parse_mode' => 'HTML'
            ]);
        }

        // Поиск пользователя
        $user = User::find($userId);
        if (!$user) {
            Log::warning('User not found for linking', ['user_id' => $userId]);
            return $this->replyWithMessage([
                'text' => '❌ Пользователь не найден в системе',
                'parse_mode' => 'HTML'
            ]);
        }

        $from = $message->getFrom();
        $telegramId = $from->getId();

        // Проверка на существующую привязку
        $existingLink = User::where('telegram_user_id', $telegramId)
                          ->where('id', '!=', $userId)
                          ->first();
        
        if ($existingLink) {
            Log::warning('Telegram account already linked', [
                'telegram_id' => $telegramId,
                'existing_user' => $existingLink->id
            ]);
            
            return $this->replyWithMessage([
                'text' => "❌ Этот Telegram аккаунт уже привязан к другому пользователю.\n\n"
                        . "Обратитесь к администратору для решения проблемы.",
                'parse_mode' => 'HTML'
            ]);
        }

        // Создание/обновление записи TelegramUser
        $telegramUser = TelegramUser::updateOrCreate(
            ['telegram_id' => $telegramId],
            [
                'first_name' => $from->getFirstName(),
                'last_name' => $from->getLastName(),
                'username' => $from->getUsername(),
            ]
        );

        // Привязка к пользователю
        $user->telegram_user_id = $telegramUser->id;
        $user->save();

        Log::info('Successfully linked Telegram account', [
            'user_id' => $userId,
            'telegram_id' => $telegramId
        ]);

        // Успешный ответ
        $this->replyWithMessage([
            'text' => "✅ <b>Аккаунт успешно привязан!</b>\n\n"
                    . "👤 Ваш профиль:\n"
                    . "Имя: <b>{$user->name}</b>\n"
                    . "Email: <b>{$user->email}</b>\n\n"
                    . "Теперь вы можете вернуться на сайт.",
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Перейти на сайт', 'url' => env('WEBHOOK_URL')]
                    ]
                ]
            ])
        ]);
    }

    protected function sendWelcomeMessage($message)
    {
        $from = $message->getFrom();
        $name = $from->getFirstName() ?? 'пользователь';
        $username = $from->getUsername() ? "@{$from->getUsername()}" : '';
        
        $this->replyWithMessage([
            'text' => "👋 <b>Привет, {$name} {$username}!</b>\n\n"
                    . "Я — бот для привязки Telegram аккаунта к системе.\n\n"
                    . "Для привязки аккаунта:\n"
                    . "1. Авторизуйтесь на сайте\n"
                    . "2. Перейдите в настройки профиля\n"
                    . "3. Используйте кнопку 'Привязать Telegram'\n\n"
                    . "<i>Если у вас возникли проблемы, обратитесь к hr-менеджеру.</i>",
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => 'Открыть сайт', 'url' => env('WEBHOOK_URL')]
                    ]
                ]
            ])
        ]);
    }
}