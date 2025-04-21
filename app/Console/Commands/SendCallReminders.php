<?php

namespace App\Console\Commands;

use App\Models\Call;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Notifications\CallReminderNotification;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Log;

class SendCallReminders extends Command
{
    protected $signature = 'calls:send-reminders';
    protected $description = 'Send reminders for upcoming calls 10 minutes before start';

    public function handle()
    {
        Log::info('Starting calls:send-reminders command execution');
        $now = Carbon::now();
        $tenMinutesLater = $now->copy()->addMinutes(10);

        Log::debug("Looking for calls at {$tenMinutesLater->format('Y-m-d H:i:s')}");

        $upcomingCalls = Call::whereDate('date', $tenMinutesLater->format('Y-m-d'))
            ->whereTime('time', '>=', $tenMinutesLater->format('H:i:s'))
            ->whereTime('time', '<=', $tenMinutesLater->copy()->addMinute()->format('H:i:s'))
            ->get();

        Log::info("Found {$upcomingCalls->count()} upcoming calls to notify");

        foreach ($upcomingCalls as $call) {
            Log::debug("Processing call ID: {$call->id} for {$call->date} {$call->time}");
            
            $participants = [
                $call->candidate,
                $call->tutor,
                $call->hr_manager
            ];

            foreach ($participants as $user) {
                if (!$user) {
                    Log::debug("Skipping null participant for call {$call->id}");
                    continue;
                }

                Log::info("Sending notification to user {$user->id} ({$user->email})");

                try {
                    // Email notification
                    $user->notify(new CallReminderNotification($call));
                    Log::debug("Email notification sent to {$user->email}");
                    
                    // Telegram notification
                    if ($user->telegram_user_id && $user->telegramUser) {
                        $meetingLink = $call->meeting_link;
                        if (!preg_match('/^https?:\/\//i', $meetingLink)) {
                            $meetingLink = 'https://' . $meetingLink;
                        }

                        $message = "⏰ *Напоминание о созвоне*\n\n"
                            . "Через 10 минут начнется созвон.\n\n"
                            . "🕒 *Дата и время:* " . \Carbon\Carbon::parse($call->date)->format('d.m.Y') . " в " . \Carbon\Carbon::parse($call->time)->format('H:i') . "\n\n"
                            . "🔗 *Ссылка для подключения:*\n"
                            . $meetingLink . "\n\n"
                            . "Пожалуйста, не опаздывайте!";

                        Telegram::sendMessage([
                            'chat_id' => $user->telegramUser->telegram_id,
                            'text' => $message,
                            'parse_mode' => 'Markdown',
                            'disable_web_page_preview' => false
                        ]);
                        
                        Log::debug("Telegram notification sent to {$user->telegramUser->telegram_id}");
                    } else {
                        Log::debug("User {$user->id} has no valid Telegram connection, skipping Telegram notification");
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send notification to user {$user->id}: " . $e->getMessage());
                }
            }
        }

        $message = "Completed. Sent reminders for {$upcomingCalls->count()} calls";
        $this->info($message);
        Log::info($message);
    }
}