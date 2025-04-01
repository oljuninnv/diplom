<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Task;

class ApplicationApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $tutor;
    public $hrManager;
    public $task;

    public function __construct(User $user, User $tutor, User $hrManager, Task $task)
    {
        $this->user = $user;
        $this->tutor = $tutor;
        $this->hrManager = $hrManager;
        $this->task = $task;
    }

    public function build()
    {
        return $this->subject('ATWINTA: Ваша заявка одобрена')
                   ->view('emails.application_approved');
    }
}