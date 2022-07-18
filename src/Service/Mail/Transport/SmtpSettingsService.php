<?php

namespace App\Service\Mail\Transport;

class SmtpSettingsService
{
    public function getUsername(): string
    {
        return 'username';
    }

    public function getPassword(): string
    {
        return 'password';
    }

    public function getHost(): string
    {
        return 'example.com';
    }

    public function getPort(): int
    {
        return 465;
    }
}
