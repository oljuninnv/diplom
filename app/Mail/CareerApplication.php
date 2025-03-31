<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CareerApplication extends Mailable
{
    use Queueable, SerializesModels;

    public $applicationData;
    public $adminEmail;

    public function __construct($applicationData)
    {
        $this->applicationData = $applicationData;
        $this->adminEmail = config('mail.from.address'); // Получаем email из .env
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->to($this->adminEmail)
            ->subject('Новая заявка с карьерной страницы: ' . $this->applicationData['position'])
            ->view('emails.admins.application')
            ->attach(storage_path('app/public/' . $this->applicationData['resume_path']), [
                'as' => 'resume_' . $this->applicationData['name'] . '.' . pathinfo($this->applicationData['resume_path'], PATHINFO_EXTENSION),
                'mime' => $this->getMimeType($this->applicationData['resume_path'])
            ]);
    }

    protected function getMimeType($path)
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        return [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ][$ext] ?? 'application/octet-stream';
    }
}