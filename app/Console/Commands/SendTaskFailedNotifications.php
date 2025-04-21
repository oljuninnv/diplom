<?php

namespace App\Console\Commands;

use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Notifications\TaskFailedNotification;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;

class SendTaskFailedNotifications extends Command
{
    protected $signature = 'tasks:send-failed-notifications';
    protected $description = 'Send notifications about failed tasks (missed deadline)';

    public function handle()
    {
        Log::info('Starting tasks:send-failed-notifications command execution');
        $now = Carbon::now();

        Log::debug("Looking for tasks with end_date before {$now->format('Y-m-d')}");

        $failedTasks = TaskStatus::where('status', '!=', 'completed')
            ->whereDate('end_date', '<', $now->format('Y-m-d'))
            ->whereNull('failed_notification_sent_at')
            ->with(['user', 'tutor', 'hr_manager'])
            ->get();

        Log::info("Found {$failedTasks->count()} failed tasks to notify");

        foreach ($failedTasks as $taskStatus) {
            Log::debug("Processing task status ID: {$taskStatus->id} for task {$taskStatus->task_id}");

            try {
                // Notify candidate
                $this->notifyUser($taskStatus->user, $taskStatus, true);

                // Notify tutor and HR
                if ($taskStatus->tutor) {
                    $this->notifyUser($taskStatus->tutor, $taskStatus, false);
                }

                if ($taskStatus->hr_manager) {
                    $this->notifyUser($taskStatus->hr_manager, $taskStatus, false);
                }

                $taskStatus->update(['failed_notification_sent_at' => $now]);
                Log::debug("Marked task status {$taskStatus->id} as notified");
            } catch (\Exception $e) {
                Log::error("Failed to process task status {$taskStatus->id}: " . $e->getMessage());
            }
        }

        $message = "Completed. Sent notifications for {$failedTasks->count()} failed tasks";
        $this->info($message);
        Log::info($message);
    }

    protected function notifyUser(User $user, TaskStatus $taskStatus, bool $isCandidate)
    {
        if (!$user) {
            Log::debug("Skipping null user for task status {$taskStatus->id}");
            return;
        }

        Log::info("Sending notification to user {$user->id} ({$user->email})");

        try {
            // Email notification
            $notification = new TaskFailedNotification($taskStatus, $isCandidate ? null : $taskStatus->user);
            $user->notify($notification);
            Log::debug("Email notification sent to {$user->email}");

            // Telegram notification
            if ($user->telegramUser) {
                $this->sendTelegramNotification($user, $taskStatus, $isCandidate);
                Log::debug("Telegram notification sent to {$user->telegramUser->telegram_id}");
            } else {
                Log::debug("User {$user->id} has no Telegram ID, skipping Telegram notification");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send notification to user {$user->id}: " . $e->getMessage());
            throw $e;
        }
    }

    protected function sendTelegramNotification(User $user, TaskStatus $taskStatus, bool $isCandidate)
    {
        if ($isCandidate) {
            $message = "❌ Тестовое задание просрочено\n\n"
                . "Вы не выполнили тестовое задание в срок.\n\n"
                . "Задание: {$taskStatus->task->name}\n"
                . "Срок сдачи: {$taskStatus->end_date->format('d.m.Y')}";
        } else {
            $message = "❌ Кандидат не выполнил задание\n\n"
                . "Кандидат {$taskStatus->user->name} не выполнил тестовое задание в срок.\n\n"
                . "Задание: {$taskStatus->task->name}\n"
                . "Срок сдачи: {$taskStatus->end_date->format('d.m.Y')}";
        }

        Telegram::sendMessage([
            'chat_id' => $user->telegramUser->telegram_id,
            'text' => $message
        ]);
    }
}