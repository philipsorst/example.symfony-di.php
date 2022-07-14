<?php

namespace App\Service\Newsletter;

use App\Model\NewsletterMail;
use App\Service\Mail\Mailer\MailerInterface;

class NewsletterService
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    /**
     * @param list<string> $recipients
     *
     * @return bool
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
}
