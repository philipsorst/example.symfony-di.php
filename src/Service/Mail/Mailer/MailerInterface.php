<?php

namespace App\Service\Mail\Mailer;

use App\Model\Email;

interface MailerInterface
{
    public function sendMail(Email $email): bool;
}
