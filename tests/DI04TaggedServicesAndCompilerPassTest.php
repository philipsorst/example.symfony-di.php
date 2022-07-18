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
    /**
     * You can tag services in order to make them accessible by this tag when building the container. The easiest
     * way to do this is to register a BaseClass which should automatically be tagged but you can also do it by hand.
     */
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

    /**
     * The same is possible in YAML.
     */
    public function testInstanceOf(): void
    {
        $containerBuilder = new ContainerBuilder();

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('tagged_services_instanceof.yaml');

        $containerBuilder->compile();

        $ids = $containerBuilder->findTaggedServiceIds('mail.transport');
        self::assertCount(2, $ids);
    }

    /**
     * You can now use these tagged services to inject them into other services. A CompilerPass is usually the right
     * place to do so.
     */
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

    /**
     * A neat way to do this just by configuration is to inject all services with a specific tag as an iterable.
     */
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

    /**
     * Obviously we should be able to change the priorities to imply an order. The higher the number, the earlier the
     * agged service will be located in the collection.
     */
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
