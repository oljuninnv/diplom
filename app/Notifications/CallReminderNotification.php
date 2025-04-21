<?php

namespace App\Notifications;

use App\Models\Call;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CallReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Call $call)
    {
        Log::debug("Creating CallReminderNotification for call {$call->id}");
    }

    public function via($notifiable)
    {
        Log::debug("Determining delivery channels for user {$notifiable->id}");
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        Log::info("Sending email reminder for call {$this->call->id} to {$notifiable->email}");

        return (new MailMessage)
            ->subject('Напоминание о предстоящем созвоне')
            ->view('emails.call_reminder', [
                'user' => $notifiable,
                'call' => $this->call
            ]);
    }

    public function reportError(\Exception $e)
    {
        Log::error("Notification failed: " . $e->getMessage());
    }
}