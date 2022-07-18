<?php

namespace App\Tests;

use App\Service\Mail\Mailer\MailerInterface;
use App\Service\Mail\Mailer\SendmailMailer;
use App\Service\Newsletter\MonitoringNewsletterService;
use App\Service\Newsletter\NewsletterService;
use ArgumentCountError;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class DI01BasicTest extends TestCase
{
    /**
     * In a traditional approach dependencies between software modules are hard coupled from top to bottom.
     * With Dependency Injection the coupling becomes loose, dependending more on interfaces and abstractions,
     * making an application configurable by inversing the control.
     * This fosters architectural patterns like SOLID.
     */
    public function testWhyDI(): void
    {
        $mailer = new SendmailMailer();
        $newsletterService = new NewsletterService($mailer);

        self::assertTrue($newsletterService->sendNewsletters(['user@example.com']));
    }

    /**
     * By default services are private. That means they cannot be accessed directly which allows for consistent
     * error handling and container optimization.
     */
    public function testFailNotPublic(): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register(MailerInterface::class, SendmailMailer::class);
        $containerBuilder->register(NewsletterService::class, NewsletterService::class);

        $containerBuilder->compile();

        $this->expectException(ServiceNotFoundException::class);
        $containerBuilder->get(NewsletterService::class);
    }

    /**
     * The container will check for circular dependencies, missing arguments and so on and throws corresponding errors.
     */
    public function testFailArgumentMissing(): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register(MailerInterface::class, SendmailMailer::class);
        $containerBuilder->register(NewsletterService::class, NewsletterService::class)
            ->setPublic(true);

        $containerBuilder->compile();

        $this->expectException(ArgumentCountError::class);
        $containerBuilder->get(NewsletterService::class);
    }

    /**
     * With a working DI container we can access the defined public services with all dependencies resolved and use
     * them based on their contracts.
     */
    public function testWorking(): void
    {
        $containerBuilder = new ContainerBuilder();
        $mailerDefinition = $containerBuilder->register(MailerInterface::class, SendmailMailer::class);
        $containerBuilder->register(NewsletterService::class, NewsletterService::class)
            ->setPublic(true)
            ->addArgument($mailerDefinition);

        $containerBuilder->compile();

        $newsletterService = $containerBuilder->get(NewsletterService::class);
        self::assertInstanceOf(NewsletterService::class, $newsletterService);

        self::assertTrue($newsletterService->sendNewsletters(['user@example.com']));
    }

    /**
     * While building the container with plain PHP Definitions is the most verbose way, the most common way is to do
     * it via config files usually written in YAML.
     */
    public function testYaml(): void
    {
        $containerBuilder = new ContainerBuilder();
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('minimal.yaml');

        $containerBuilder->compile();

        $newsletterService = $containerBuilder->get(NewsletterService::class);
        self::assertInstanceOf(NewsletterService::class, $newsletterService);

        self::assertTrue($newsletterService->sendNewsletters(['user@example.com']));
    }

    /**
     * The Symfony DI container allows for autowiring, that means if dependencies can be resolved automatically
     * we do not explicetly need to specify them.
     */
    public function testAutowire(): void
    {
        $containerBuilder = new ContainerBuilder();
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('autowire.yaml');

        $containerBuilder->compile();

        $newsletterService = $containerBuilder->get(NewsletterService::class);
        self::assertInstanceOf(NewsletterService::class, $newsletterService);

        self::assertTrue($newsletterService->sendNewsletters(['user@example.com']));
    }

    /**
     * However if there are multiple implementations for a required interface or abstract class the container building
     * will fail as it does not know which implementation to use.
     */
    public function testScanFailMissingAlias(): void
    {
        $containerBuilder = new ContainerBuilder();
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('scan.yaml');

        $this->expectException(RuntimeException::class);
        $containerBuilder->compile();
    }

    /**
     * In order to choose the default interface implementation, we can ALIAS it.
     */
    public function testInterfaceAlias(): void
    {
        $containerBuilder = new ContainerBuilder();
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('interface_alias.yaml');

        $containerBuilder->compile();

        $newsletterService = $containerBuilder->get(NewsletterService::class);
        self::assertInstanceOf(NewsletterService::class, $newsletterService);

        self::assertTrue($newsletterService->sendNewsletters(['user@example.com']));
    }

    /**
     * Parameters work like variables within the dependency injection context.
     */
    public function testParameters(): void
    {
        $containerBuilder = new ContainerBuilder();
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('parameters.yaml');

        $containerBuilder->compile();

        $newsletterService = $containerBuilder->get(NewsletterService::class);
        self::assertInstanceOf(NewsletterService::class, $newsletterService);

        self::assertTrue($newsletterService->sendNewsletters(['user@example.com']));
    }

    /**
     * We can define optional service arguments if we can't or won't assert that a dependency is available.
     */
    public function testOptional(): void
    {
        $containerBuilder = new ContainerBuilder();
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('optional.yaml');

        $containerBuilder->compile();

        $newsletterService = $containerBuilder->get(NewsletterService::class);
        self::assertInstanceOf(MonitoringNewsletterService::class, $newsletterService);

        self::assertTrue($newsletterService->sendNewsletters(['user@example.com']));
    }
}
