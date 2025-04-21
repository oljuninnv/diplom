<?php

namespace App\Notifications;

use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class TaskFailedNotification extends Notification
{
    public function __construct(
        public TaskStatus $taskStatus,
        public ?User $candidate = null
    ) {
        if (!$this->candidate) {
            $this->candidate = $taskStatus->user;
        }
    }

    public function via($notifiable)
    {
        // Отправляем только email, Telegram будем обрабатывать отдельно
        return ['mail'];
    }

    public function sendTelegramNotification($notifiable)
    {
        if (!$notifiable->telegramUser || !$notifiable->telegramUser->telegram_id) {
            Log::info("User {$notifiable->id} has no Telegram chat ID");
            return false;
        }

        $isCandidate = $notifiable->id === $this->candidate->id;
        
        $text = $isCandidate 
            ? "❌ *Ваше тестовое задание просрочено*\n\n"
              . "Вы не выполнили тестовое задание в срок.\n\n"
              . "*Задание:* {$this->taskStatus->task->title}\n"
              . "*Срок сдачи:* {$this->taskStatus->end_date->format('d.m.Y')}"
              ."Свяжитесь с вашим куратором/hr-менеджером для уточнения дальнейших действий."
            : "❌ *Кандидат не выполнил задание*\n\n"
              . "Кандидат {$this->candidate->name} не выполнил тестовое задание в срок.\n\n"
              . "*Задание:* {$this->taskStatus->task->title}\n"
              . "*Срок сдачи:* {$this->taskStatus->end_date->format('d.m.Y')}";

        try {
            Telegram::sendMessage([
                'chat_id' => $notifiable->telegramUser->telegram_id,
                'text' => $text,
                'parse_mode' => 'Markdown'
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error("Telegram send failed: " . $e->getMessage());
            return false;
        }
    }

    public function toMail($notifiable)
    {
        $isCandidate = $notifiable->id === $this->candidate->id;
        
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($isCandidate ? 'Ваше тестовое задание просрочено' : 'Кандидат не выполнил тестовое задание')
            ->view('emails.task_failed', [
                'user' => $notifiable,
                'taskStatus' => $this->taskStatus,
                'isCandidate' => $isCandidate,
                'candidate' => $this->candidate
            ]);
    }
}