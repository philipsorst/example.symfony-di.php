<?php

namespace App\Service\Mail\Mailer;

use App\Model\Email;
use App\Service\Mail\Transport\TransportInterface;

class GenericMailer implements MailerInterface
{
    public function __construct(public readonly TransportInterface $transport)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function sendMail(Email $email): bool
    {
        return $this->transport->sendMail($email);
    }
}
