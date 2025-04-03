<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $userData;
    public $taskData;

    public function __construct($userData, $taskData)
    {
        $this->userData = $userData;
        $this->taskData = $taskData;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->userData->email)
            ->subject('Ваше решение принято на проверку')
            ->view('emails.task_confirmation');
    }
}