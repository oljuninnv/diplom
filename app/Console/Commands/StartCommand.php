<?php

namespace App\Console\Commands;

use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = 'Введите команду, чтобы начать!';

    public function handle()
    {
        $message = $this->getUpdate()->getMessage();
        $chatId = $message->getChat()->getId();

        $this->replyWithMessage([
            'chat_id' => $chatId,
            'text' => 'Добрый день, рад вас видеть! Данный бот предназначен для отправки уведомлений о всех событиях при выполнении тестового задания.',
        ]);
    }
}