<?php

namespace App\Service\Mail\Transport;

use App\Model\Email;

class SendmailTransport extends BaseTransport implements TransportInterface
{
    /**
     * {@inheritdoc}
     */
    public function sendMail(Email $email): bool
    {
        // ...Implementation
        return true;
    }
}
