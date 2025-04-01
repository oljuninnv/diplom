<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Task;

class UserAddedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $tutor;
    public $hrManager;
    public $task;
    public $endDate;
    public $adminEmail;

    public function __construct(User $user, User $tutor, User $hrManager, Task $task, $endDate = null)
    {
        $this->user = $user;
        $this->tutor = $tutor;
        $this->hrManager = $hrManager;
        $this->task = $task;
        $this->endDate = $endDate;
        $this->adminEmail = config('mail.admin_address', config('mail.from.address'));
    }

    public function build()
    {
        if (empty($this->adminEmail)) {
            throw new \RuntimeException('Admin email address is not configured');
        }

        return $this->to($this->adminEmail)
                   ->subject('ATWINTA: Пользователю назначено тестовое задание')
                   ->view('emails.admins.user_added'); // Убедитесь что путь правильный
    }
}