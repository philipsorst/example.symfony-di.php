<?php

namespace App\Tests;

use App\Service\Newsletter\MonitoringNewsletterService;
use App\Service\Newsletter\NewsletterService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ChapterDecoratedTest extends TestCase
{
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
}
