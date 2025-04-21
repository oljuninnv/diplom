<?php

namespace App\Notifications;

use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class TaskFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TaskStatus $taskStatus,
        public ?User $candidate = null
    ) {
        if (!$this->candidate) {
            $this->candidate = $taskStatus->user;
        }
        
        Log::debug("Creating TaskFailedNotification for task status {$taskStatus->id}");
    }

    public function via($notifiable)
    {
        Log::debug("Determining delivery channels for user {$notifiable->id}");
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $isCandidate = $notifiable->id === $this->candidate->id;
        $logPrefix = $isCandidate ? "Candidate" : "Staff";
        
        Log::info("{$logPrefix} notification for task {$this->taskStatus->task_id} to {$notifiable->email}");

        $message = (new MailMessage);

        if ($isCandidate) {
            $message->subject('Ваше тестовое задание просрочено')
                ->line('К сожалению, вы не выполнили тестовое задание в срок.')
                ->line('Задание: ' . $this->taskStatus->task->name)
                ->line('Срок сдачи: ' . $this->taskStatus->end_date->format('d.m.Y'));
        } else {
            $message->subject('Кандидат не выполнил тестовое задание')
                ->line('Кандидат ' . $this->candidate->name . ' не выполнил тестовое задание в срок.')
                ->line('Задание: ' . $this->taskStatus->task->name)
                ->line('Срок сдачи: ' . $this->taskStatus->end_date->format('d.m.Y'));
        }

        return $message;
    }

    public function reportError(\Exception $e)
    {
        Log::error("Notification failed: " . $e->getMessage());
    }
}