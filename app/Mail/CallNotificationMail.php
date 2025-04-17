<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CallNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        $subject = match ($this->data['action']) {
            'scheduled' => 'Созвон назначен',
            'updated' => 'Изменение в созвоне',
            'cancelled' => 'Созвон отменен',
            default => 'Уведомление о созвоне'
        };

        return $this->subject($subject)
            ->view('emails.call_notification')
            ->with(['data' => $this->data]);
    }
}