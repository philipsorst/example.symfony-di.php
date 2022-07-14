<?php

namespace App\Tests;

use App\DependencyInjection\Mailer\MailerExtension;
use App\Service\Mail\Mailer\GenericMailer;
use App\Service\Mail\Mailer\MailerInterface;
use App\Service\Mail\Transport\SendmailTransport;
use App\Service\Mail\Transport\SmtpTransport;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ChapterFactoryTest extends TestCase
{
    public function testStaticFactory(): void
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->registerExtension(new MailerExtension());

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('factory_static.yaml');

        $containerBuilder->compile();

        $mailer = $containerBuilder->get(MailerInterface::class);
        self::assertInstanceOf(GenericMailer::class, $mailer);
        self::assertInstanceOf(SendmailTransport::class, $mailer->transport);
    }

    public function testInvokableFactory(): void
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->registerExtension(new MailerExtension());

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('factory_invokable.yaml');

        $containerBuilder->compile();

        $mailer = $containerBuilder->get(MailerInterface::class);
        self::assertInstanceOf(GenericMailer::class, $mailer);
        self::assertInstanceOf(SmtpTransport::class, $mailer->transport);
    }
}
