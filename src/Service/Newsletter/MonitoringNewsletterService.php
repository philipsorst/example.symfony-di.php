<?php

namespace App\Service\Newsletter;

class MonitoringNewsletterService implements NewsletterServiceInterface
{
    public function __construct(private readonly NewsletterServiceInterface $decoratedNewsletterService)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function sendNewsletters(array $recipients): bool
    {
        return $this->decoratedNewsletterService->sendNewsletters(['monitor@example.com', ...$recipients]);
    }
}
