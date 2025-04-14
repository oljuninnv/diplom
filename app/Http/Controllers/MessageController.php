<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TelegramUser;
use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Telegram\Bot\BotsManager;

class MessageController extends Controller
{
    protected BotsManager $botsManager;

    public function __construct(BotsManager $botsManager)
    {
        $this->botsManager = $botsManager;
    }

    public function __invoke(Request $request)
    {
        try {
            $telegram = new Api(config('telegram.bot_token'));
            $update = $telegram->getWebhookUpdate();

            // Обработка команды /start с deep link
            if (
                $update->getMessage() &&
                str_starts_with($text = $update->getMessage()->getText(), '/start link_')
            ) {

                $parts = explode('_', $text);
                if (count($parts) >= 4) {
                    $user_id = $parts[2];
                    $token = $parts[3];

                    $user = User::where('id', $user_id)
                        ->where('telegram_link_token', $token)
                        ->first();

                    if ($user) {
                        $from = $update->getMessage()->getFrom();

                        $telegramUser = TelegramUser::updateOrCreate(
                            ['telegram_id' => $from->getId()],
                            [
                                'first_name' => $from->getFirstName(),
                                'last_name' => $from->getLastName(),
                                'username' => $from->getUsername(),
                            ]
                        );

                        $user->update([
                            'telegram_user_id' => $telegramUser->id,
                            'telegram_link_token' => null
                        ]);

                        $this->sendTelegramResponse(
                            $update->getMessage()->getChat()->getId(),
                            "✅ Ваш Telegram успешно привязан к аккаунту {$user->email}!"
                        );
                    }
                }
            }

            // Стандартная обработка команд
            $this->botsManager->bot()->commandsHandler(true);

            return response()->noContent();
        } catch (\Exception $e) {
            \Log::error('Telegram webhook error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    protected function handleDeepLink($update)
    {
        $text = $update->getMessage()->getText();
        $parts = explode('_', $text);

        if (count($parts) >= 4) {
            $user_id = $parts[2];
            $hash = $parts[3];
            $user = User::find($user_id);

            if ($user && hash_equals(sha1($user_id . env('APP_KEY')), $hash)) {
                $from = $update->getMessage()->getFrom();

                $telegramUser = TelegramUser::updateOrCreate(
                    ['telegram_id' => $from->getId()],
                    [
                        'first_name' => $from->getFirstName(),
                        'last_name' => $from->getLastName(),
                        'username' => $from->getUsername(),
                    ]
                );

                $user->update(['telegram_user_id' => $telegramUser->id]);

                $this->sendTelegramResponse(
                    $update->getMessage()->getChat()->getId(),
                    "✅ Ваш Telegram успешно привязан к аккаунту {$user->email}!"
                );
            }
        }
    }

    protected function sendTelegramResponse($chatId, $text)
    {
        $telegram = new Api(config('telegram.bot_token'));
        $telegram->sendMessage([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ]);
    }
}