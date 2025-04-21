<?php

namespace App\Console\Commands;

use App\Models\TaskStatus;
use App\Notifications\TaskFailedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Enums\TaskStatusEnum;

class SendTaskFailedNotifications extends Command
{
    protected $signature = 'tasks:send-failed-notifications';
    protected $description = 'Send notifications about failed tasks';

    public function handle()
    {
        Log::info('Starting task failure notifications');

        $tasks = TaskStatus::with(['user.telegramUser', 'tutor.telegramUser', 'hr_manager.telegramUser', 'task'])
            ->whereNotIn('status', [TaskStatusEnum::APPROVED->value,TaskStatusEnum::FAILED->value,TaskStatusEnum::UNDER_REVIEW->value])
            ->whereDate('end_date', '<', now())
            ->get();

        foreach ($tasks as $task) {
            try {
                $this->processTask($task);
            } catch (\Exception $e) {
                Log::error("Task processing failed: " . $e->getMessage(), [
                    'task_id' => $task->id,
                    'error' => $e
                ]);
                $this->error("Error with task {$task->id}: " . $e->getMessage());
            }
        }

        $this->info("Completed. Processed {$tasks->count()} tasks");
    }

    protected function processTask($task)
    {
        Log::info("Processing task status ID: {$task->id}");

        $notification = new TaskFailedNotification($task);

        // Уведомление кандидата
        $this->notifyUser($task->user, $notification, true);

        // Уведомление тьютора
        if ($task->tutor) {
            $this->notifyUser($task->tutor, $notification, false);
        }

        // Уведомление HR
        if ($task->hr_manager) {
            $this->notifyUser($task->hr_manager, $notification, false);
        }

        $task->update(['status' => TaskStatusEnum::FAILED->value]);
        Log::info("Task {$task->id} marked as failed");
    }

    protected function notifyUser($user, $notification, $isCandidate)
    {
        if (!$user) {
            Log::warning("User not found for notification");
            return;
        }

        Log::info("Notifying user {$user->id} ({$user->email})", [
            'has_telegram' => !is_null($user->telegramUser),
            'is_candidate' => $isCandidate
        ]);

        // Отправляем email через стандартный механизм
        $user->notify($notification);

        // Отправляем Telegram уведомление напрямую
        $notification->sendTelegramNotification($user);
    }
}