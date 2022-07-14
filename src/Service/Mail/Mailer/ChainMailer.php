<?php

namespace App\Service\Mail\Mailer;

use App\Model\Email;
use App\Service\Mail\Transport\TransportInterface;

class ChainMailer implements MailerInterface
{
    /**
     * @param iterable<TransportInterface> $transports
     */
    public function __construct(private readonly iterable $transports)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function sendMail(Email $email): bool
    {
        foreach ($this->transports as $transport) {
            if ($transport->sendMail($email)) {
                return true;
            }
        }

        return false;
    }
}
