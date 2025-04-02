<?php

namespace App\DTO;

class PasswordResetDTO
{
    public string $email;
    public string $token;
    public string $url;
    public string $title;
    public string $body;

    public function __construct(string $email, string $token, string $url, string $title, string $body)
    {
        $this->email = $email;
        $this->token = $token;
        $this->url = $url;
        $this->title = $title;
        $this->body = $body;
    }
}