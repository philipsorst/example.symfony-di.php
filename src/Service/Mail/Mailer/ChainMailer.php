<?php

namespace App\Service\Mail\Mailer;

use App\Model\Email;
use App\Service\Mail\Transport\TransportInterface;
use RuntimeException;

class ChainMailer implements MailerInterface
{
    /**
     * @param iterable<TransportInterface> $transports
     */
    public function __construct(public iterable $transports = [])
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

    public function addTransport(TransportInterface $transport): void
    {
        if (!is_array($this->transports)) {
            throw new RuntimeException('Transports is not an array');
        }

        $this->transports[] = $transport;
    }
}
