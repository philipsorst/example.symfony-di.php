<?php

namespace App\Service\Mail\Transport;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

abstract class BaseTransport
{
    protected LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getLogger(): NullLogger|LoggerInterface
    {
        return $this->logger;
    }
}
