<?php

namespace App\Tests;

use App\Service\Newsletter\NewsletterService;
use PHPUnit\Framework\TestCase;
use ProjectServiceContainer;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class DI02ChapterCachingTest extends TestCase
{
    public function testCaching(): void
    {
        $file = sys_get_temp_dir() . '/example_container.php';
        if (file_exists($file)) {
            unlink($file);
        }
        self::assertFileDoesNotExist($file);

        $containerBuilder = new ContainerBuilder();
        $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../config'));
        $loader->load('autowire.yaml');

        $containerBuilder->compile();

        $dumper = new PhpDumper($containerBuilder);
        $dumpedContainer = $dumper->dump();
        self::assertIsString($dumpedContainer);
        file_put_contents($file, $dumpedContainer);

        require_once $file;
        /** @psalm-suppress  UndefinedClass */
        $container = new ProjectServiceContainer();
        self::assertInstanceOf(ContainerInterface::class, $container);

        $newsletterService = $container->get(NewsletterService::class);
        self::assertInstanceOf(NewsletterService::class, $newsletterService);

        self::assertTrue($newsletterService->sendNewsletters(['user@example.com']));
    }
}
