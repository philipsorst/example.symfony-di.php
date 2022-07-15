<?php

namespace App\Service\Newsletter;

interface NewsletterServiceInterface
{
    /**
     * @param list<string> $recipients
     *
     * @return bool
     */
    public function sendNewsletters(array $recipients): bool;
}
