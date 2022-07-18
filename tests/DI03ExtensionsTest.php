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

class DI03ExtensionsTest extends TestCase
{
    public function testExtensionAndCompilerPass(): void
    {
        $_SERVER['MAILER_USERNAME'] = 'username';
        $_SERVER['MAILER_PASSWORD'] = 'password';
        $_SERVER['MAILER_HOST'] = 'example.com';
        $_SERVER['MAILER_PORT'] = 465;

        $containerBuilder = new ContainerBuilder();

        $containerBuilder->registerExtension(new MailerExtension());

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('extension_config.yaml');

        $containerBuilder->compile(true);

        $transport = $containerBuilder->get(TransportInterface::class);
        self::assertInstanceOf(SmtpTransport::class, $transport);
        self::assertEquals('username', $transport->username);
        self::assertEquals('password', $transport->password);
        self::assertEquals('example.com', $transport->host);
        self::assertEquals(465, $transport->port);

        $newsletterService = $containerBuilder->get(NewsletterService::class);
        self::assertInstanceOf(NewsletterService::class, $newsletterService);

        self::assertTrue($newsletterService->sendNewsletters(['user@example.com']));
    }
}
