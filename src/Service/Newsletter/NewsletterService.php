<?php

namespace App\Service\Newsletter;

use App\Model\NewsletterMail;
use App\Service\Mail\Mailer\MailerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class NewsletterService implements NewsletterServiceInterface
{
    private LoggerInterface $logger;

    public function __construct(private readonly MailerInterface $mailer)
    {
        $this->logger = new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function sendNewsletters(array $recipients): bool
    {
        foreach ($recipients as $recipient) {
            $newsletter = new NewsletterMail($recipient);
            if (!$this->mailer->sendMail($newsletter)) {
                return false;
            }
        }

        return true;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
