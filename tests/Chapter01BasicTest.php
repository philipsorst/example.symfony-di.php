<?php

namespace App\Tests;

use App\Service\Mail\Mailer\MailerInterface;
use App\Service\Mail\Mailer\SendmailMailer;
use App\Service\Newsletter\NewsletterService;
use ArgumentCountError;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Chapter01BasicTest extends TestCase
{
    public function testWhyDI(): void
    {
        $mailer = new SendmailMailer();
        $newsletterService = new NewsletterService($mailer);

        self::assertTrue($newsletterService->sendNewsletters(['user@example.com']));
    }

    public function testFailNotPublic(): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register(MailerInterface::class, SendmailMailer::class);
        $containerBuilder->register(NewsletterService::class, NewsletterService::class);

        $containerBuilder->compile();

        $this->expectException(ServiceNotFoundException::class);
        $containerBuilder->get(NewsletterService::class);
    }

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

    public function testScanFailMissingAlias(): void
    {
        $containerBuilder = new ContainerBuilder();
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('scan.yaml');

        $this->expectException(RuntimeException::class);
        $containerBuilder->compile();
    }

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
}
