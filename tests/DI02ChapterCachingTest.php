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
    /**
     * Usually the container needs to be built on every call/request. This can be costly as the configuration and
     * extensions have to be parsed and resolved.
     * We can dump the container to disk after this process instead and use this fully resolved instance the next time
     * instead. If the configuration changes we need to redump if of course.
     */
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
