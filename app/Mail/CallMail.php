<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CallMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailData;

    public function __construct(array $emailData)
    {
        $this->emailData = $emailData;
    }

    public function build()
    {
        return $this->subject('Назначен созвон - ' . $this->emailData['companyName'])
                    ->view('emails.call_assigned')
                    ->with($this->emailData);
    }
}