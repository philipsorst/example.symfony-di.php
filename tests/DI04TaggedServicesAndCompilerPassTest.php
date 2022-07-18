<?php

namespace App\Tests;

use App\DependencyInjection\Mailer\MailTransportCompilerPass;
use App\Service\Mail\Mailer\ChainMailer;
use App\Service\Mail\Transport\SendmailTransport;
use App\Service\Mail\Transport\SmtpTransport;
use App\Service\Mail\Transport\TransportInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class DI04TaggedServicesAndCompilerPassTest extends TestCase
{
    public function testAutoconfiguration(): void
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder
            ->registerForAutoconfiguration(TransportInterface::class)
            ->addTag('mail.transport')
            ->setPublic(true);

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('tagged_services_autoconfiguration.yaml');

        $containerBuilder->compile();

        $ids = $containerBuilder->findTaggedServiceIds('mail.transport');
        self::assertCount(2, $ids);
    }

    public function testInstanceOf(): void
    {
        $containerBuilder = new ContainerBuilder();

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('tagged_services_instanceof.yaml');

        $containerBuilder->compile();

        $ids = $containerBuilder->findTaggedServiceIds('mail.transport');
        self::assertCount(2, $ids);
    }

    public function testCompilerPass(): void
    {
        $containerBuilder = new ContainerBuilder();

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('compiler_pass.yaml');

        $containerBuilder->addCompilerPass(new MailTransportCompilerPass());

        $containerBuilder->compile();

        $chainMailer = $containerBuilder->get(ChainMailer::class);
        self::assertInstanceOf(ChainMailer::class, $chainMailer);

        self::assertCount(2, $chainMailer->transports);
    }

    public function testTaggedIterator(): void
    {
        $containerBuilder = new ContainerBuilder();

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('tagged_iterator.yaml');

        $containerBuilder->compile();

        $chainMailer = $containerBuilder->get(ChainMailer::class);
        self::assertInstanceOf(ChainMailer::class, $chainMailer);

        self::assertCount(2, $chainMailer->transports);
    }

    public function testPriorities(): void
    {
        $containerBuilder = new ContainerBuilder();

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('priorities.yaml');

        $containerBuilder->compile();

        $chainMailer = $containerBuilder->get(ChainMailer::class);
        self::assertInstanceOf(ChainMailer::class, $chainMailer);

        $transports = [...$chainMailer->transports];
        self::assertCount(2, $chainMailer->transports);
        self::assertInstanceOf(SmtpTransport::class, $transports[0]);
        self::assertInstanceOf(SendmailTransport::class, $transports[1]);
    }
}
