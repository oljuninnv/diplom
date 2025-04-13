<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TaskStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $status;
    public $taskId;
    public $comment;
    public $filePath;

    public function __construct($user, $status, $taskId, $comment = null, $filePath = null)
    {
        $this->user = $user;
        $this->status = $status;
        $this->taskId = $taskId;
        $this->comment = $comment;
        $this->filePath = $filePath;
    }

    public function build()
    {
        $subject = match($this->status) {
            'revision' => 'Задание отправлено на доработку',
            'approved' => 'Задание одобрено',
            'failed' => 'Задание не принято',
            default => 'Статус задания изменен'
        };

        $mail = $this->subject($subject)
                    ->view('emails.task_status');

        // Если есть файл и он существует в storage
        if ($this->filePath && Storage::disk('public')->exists($this->filePath)) {
            $mail->attachFromStorageDisk('public', $this->filePath);
        }

        return $mail;
    }
}