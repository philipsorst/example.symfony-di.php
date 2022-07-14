<?php

namespace App\Tests;

use App\DependencyInjection\Mailer\MailerExtension;
use App\DependencyInjection\SmtpMailerCompilerPass;
use App\Service\Mail\Transport\SmtpTransport;
use App\Service\Mail\Transport\TransportInterface;
use App\Service\Newsletter\NewsletterService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Chapter02ExtensionsTest extends TestCase
{
    public function testExtensionAndCompilerPass(): void
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->registerExtension(new MailerExtension());

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('extension_config.yaml');

        $containerBuilder->compile();

        $transport = $containerBuilder->get(TransportInterface::class);
        self::assertInstanceOf(SmtpTransport::class, $transport);

        $newsletterService = $containerBuilder->get(NewsletterService::class);
        self::assertInstanceOf(NewsletterService::class, $newsletterService);

        self::assertTrue($newsletterService->sendNewsletters(['user@example.com']));
    }
}
