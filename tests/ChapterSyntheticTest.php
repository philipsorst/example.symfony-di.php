<?php

namespace App\Tests;

use App\Service\Mail\Mailer\MailerInterface;
use App\Service\Mail\Mailer\SendmailMailer;
use App\Service\Newsletter\NewsletterService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ChapterSyntheticTest extends TestCase
{
    public function testSynthetic(): void
    {
        $containerBuilder = new ContainerBuilder();
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('synthetic.yaml');

        $containerBuilder->set(MailerInterface::class, new SendmailMailer());

        $containerBuilder->compile();

        $newsletterService = $containerBuilder->get(NewsletterService::class);
        self::assertInstanceOf(NewsletterService::class, $newsletterService);

        self::assertTrue($newsletterService->sendNewsletters(['user@example.com']));
    }
}
