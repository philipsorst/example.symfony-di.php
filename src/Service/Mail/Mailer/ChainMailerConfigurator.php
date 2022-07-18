<?php

namespace App\Service\Mail\Mailer;

use App\Service\Mail\Transport\SendmailTransport;
use App\Service\Mail\Transport\SmtpSettingsService;
use App\Service\Mail\Transport\SmtpTransport;

class ChainMailerConfigurator
{
    public function __construct(private readonly SmtpSettingsService $smtpSettingsService)
    {
    }

    public function configure(ChainMailer $mailer): void
    {
        $mailer->addTransport(
            new SmtpTransport(
                $this->smtpSettingsService->getUsername(),
                $this->smtpSettingsService->getPassword(),
                $this->smtpSettingsService->getHost(),
                $this->smtpSettingsService->getPort()
            )
        );

        $mailer->addTransport(new SendmailTransport());
    }
}
