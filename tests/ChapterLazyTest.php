<?php

namespace App\Tests;

use App\Service\Mail\Mailer\SendmailMailer;
use PHPUnit\Framework\TestCase;
use ProxyManager\Proxy\LazyLoadingInterface;
use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ChapterLazyTest extends TestCase
{
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
}
