<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TaskSubmittedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $taskData;
    public $userData;
    public $taskFilePath;

    public function __construct($taskData, $userData)
    {
        $this->taskData = $taskData;
        $this->userData = $userData;
        $this->taskFilePath = $taskData->task->task;
    }

    public function build()
    {
        $email = $this
            ->to(config('mail.admin_address', config('mail.from.address')))
            ->subject('Новое решение задания от кандидата: ' . $this->userData->name)
            ->view('emails.task_submitted');

        // Прикрепляем файл задания, если он существует
        if (Storage::disk('public')->exists($this->taskFilePath)) {
            $filePath = Storage::disk('public')->path($this->taskFilePath);
            $originalName = $this->taskData->task->task;
            
            $email->attach($filePath, [
                'as' => $originalName,
                'mime' => $this->getMimeType($originalName)
            ]);
        }

        return $email;
    }

    protected function getMimeType($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }
}