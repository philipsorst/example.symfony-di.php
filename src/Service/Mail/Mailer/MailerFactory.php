<?php

namespace App\Service\Mail\Mailer;

use App\Service\Mail\Transport\SendmailTransport;
use App\Service\Mail\Transport\SmtpTransport;
use App\Service\Mail\Transport\TransportInterface;
use RuntimeException;

class MailerFactory
{
    public static function createDefaultMailer(): MailerInterface
    {
        return new GenericMailer(new SendmailTransport());
    }

    public function createMailer(string $transportType): MailerInterface
    {
        return new GenericMailer($this->getTransport($transportType));
    }

    private function getTransport(string $transportType): TransportInterface
    {
        return match ($transportType) {
            'sendmail' => new SendmailTransport(),
            'smtp' => new SmtpTransport('username', 'passwort', 'example.com', 25),
            default => throw new RuntimeException('Unknown transport type ' . $transportType),
        };
    }
}
