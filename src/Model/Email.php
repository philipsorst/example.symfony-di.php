<?php

namespace App\Model;

class Email
{
    public function __construct(public readonly string $recipient)
    {
    }
}
