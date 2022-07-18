<?php

namespace App\Service\Mail\Transport;

use App\Model\Email;

class SmtpTransport extends BaseTransport implements TransportInterface
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
        public readonly string $host,
        public readonly int $port = 25
    ) {
        parent::__construct();
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
