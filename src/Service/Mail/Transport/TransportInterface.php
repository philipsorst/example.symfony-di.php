<?php

namespace App\Service\Mail\Transport;

use App\Model\Email;

interface TransportInterface
{
    public function sendMail(Email $email): bool;
}
