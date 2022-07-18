<?php

namespace App\Tests;

use App\Service\Logger\EchoLogger;
use App\Service\Mail\Mailer\ChainMailer;
use App\Service\Mail\Mailer\GenericMailer;
use App\Service\Mail\Mailer\MailerInterface;
use App\Service\Mail\Mailer\SendmailMailer;
use App\Service\Mail\Transport\SendmailTransport;
use App\Service\Mail\Transport\SmtpTransport;
use App\Service\Newsletter\MonitoringNewsletterService;
use App\Service\Newsletter\NewsletterService;
use PHPUnit\Framework\TestCase;
use ProxyManager\Proxy\LazyLoadingInterface;
use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class DI05AdvancedTest extends TestCase
{
    /**
     * Symfony DI allows you to apply the factory pattern. That means another service factory is responsible for
     * creating the objects needed.
     */
    public function testStaticFactory(): void
    {
        $containerBuilder = new ContainerBuilder();

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('factory_static.yaml');

        $containerBuilder->compile();

        $mailer = $containerBuilder->get(MailerInterface::class);
        self::assertInstanceOf(GenericMailer::class, $mailer);
        self::assertInstanceOf(SendmailTransport::class, $mailer->transport);
    }

    /**
     * The factory does not need to be static.
     */
    public function testInvokableFactory(): void
    {
        $containerBuilder = new ContainerBuilder();

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('factory_invokable.yaml');

        $containerBuilder->compile();

        $mailer = $containerBuilder->get(MailerInterface::class);
        self::assertInstanceOf(GenericMailer::class, $mailer);
        self::assertInstanceOf(SmtpTransport::class, $mailer->transport);
    }

    /**
     * Sometimes the construction of services can be costly (e.g. establishing database connection). In this case we
     * can use LAZY services that will only be instantiated when they are actually used. This requires a proxy
     * manager.
     */
    public function testLazy(): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setProxyInstantiator(new RuntimeInstantiator());
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('lazy.yaml');

        $containerBuilder->compile();

        $mailer = $containerBuilder->get(SendmailMailer::class);
        self::assertInstanceOf(SendmailMailer::class, $mailer);
        self::assertContains(LazyLoadingInterface::class, class_implements($mailer));
    }

    /**
     * In rare cases it is not possible to construct an instance via the DI configuration, but we want to use an
     * already instantiated service and make it accessible via the container.
     */
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

    /**
     * We can also use the decorator pattern which is sometimes more efficient than subclassing.
     */
    public function testDecorate(): void
    {
        $containerBuilder = new ContainerBuilder();
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('decorated.yaml');

        $containerBuilder->compile();

        $newsletterService = $containerBuilder->get(NewsletterService::class);
        self::assertInstanceOf(MonitoringNewsletterService::class, $newsletterService);

        self::assertTrue($newsletterService->sendNewsletters(['user@example.com']));
    }

    /**
     * If the configuration of a service is very complex, we can use a configurator to extract this task to a
     * designated service.
     */
    public function testConfigurator(): void
    {
        $containerBuilder = new ContainerBuilder();

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('configurator.yaml');

        $containerBuilder->compile(true);

        $mailer = $containerBuilder->get(ChainMailer::class);
        self::assertInstanceOf(ChainMailer::class, $mailer);
        self::assertCount(2, $mailer->transports);
    }

    /**
     * It is also possible to use expressions when defining services that have access to service, parameter and env
     * in their context. We need the expression-language component to use them.
     */
    public function testExpression(): void
    {
        $containerBuilder = new ContainerBuilder();

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('expressions.yaml');

        $containerBuilder->compile(true);

        $transport = $containerBuilder->get(SmtpTransport::class);
        self::assertInstanceOf(SmtpTransport::class, $transport);
        self::assertEquals('username', $transport->username);
        self::assertEquals('password', $transport->password);
        self::assertEquals('example.com', $transport->host);
        self::assertEquals(465, $transport->port);
    }

    /**
     * Defining parent services can be useful if we want to add a common configuration for all parent classes.
     */
    public function testParent(): void
    {
        $containerBuilder = new ContainerBuilder();

        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('parent.yaml');

        $containerBuilder->compile(true);

        $smtpTransport = $containerBuilder->get(SmtpTransport::class);
        self::assertInstanceOf(SmtpTransport::class, $smtpTransport);
        self::assertInstanceOf(EchoLogger::class, $smtpTransport->getLogger());

        $sendmailTransport = $containerBuilder->get(SendmailTransport::class);
        self::assertInstanceOf(SendmailTransport::class, $sendmailTransport);
        self::assertInstanceOf(EchoLogger::class, $sendmailTransport->getLogger());
    }
}
