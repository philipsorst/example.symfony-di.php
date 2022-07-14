<?php

namespace App\Service\Mail\Mailer;

use App\Model\Email;
use App\Service\Mail\Transport\SendmailTransport;

class SendmailMailer implements MailerInterface
{
    private SendmailTransport $transport;

    public function __construct()
    {
        $this->transport = new SendmailTransport();
    }

    /**
     * {@inheritdoc}
     */
    public function sendMail(Email $email): bool
    {
        return $this->transport->sendMail($email);
    }
}
