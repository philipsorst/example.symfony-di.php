<?php

namespace App\Service\Logger;

use Psr\Log\AbstractLogger;
use Stringable;

class EchoLogger extends AbstractLogger
{
    /**
     * {@inheritdoc}
     */
    public function log($level, Stringable|string $message, array $context = []): void
    {
        echo sprintf("%s:%s\n", (string)$level, (string)$message);
    }
}
