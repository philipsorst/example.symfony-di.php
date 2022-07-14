<?php

namespace App\Service\Mail\Transport;

use App\Model\Email;

class SmtpTransport implements TransportInterface
{
    public function __construct(
        private readonly string $username,
        private readonly string $password,
        private readonly string $host,
        private readonly int $port
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function sendMail(Email $email): bool
    {
        // ...Implementation
        return true;
    }
}
