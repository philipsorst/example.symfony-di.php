<?php

namespace App\DependencyInjection\Mailer;

use App\Service\Mail\Transport\SmtpTransport;
use App\Service\Mail\Transport\TransportInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MailerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../../config'));
        $loader->load('extension_services.yaml');

        $smtpConfig = (array)$config['smtp'];
        if ((bool)$smtpConfig['enabled']) {
            $container->getDefinition(SmtpTransport::class)
                ->addArgument((string)$smtpConfig['username'])
                ->addArgument((string)$smtpConfig['password'])
                ->addArgument((string)$smtpConfig['host'])
                ->addArgument((int)$smtpConfig['port']);
            $container
                ->setAlias(TransportInterface::class, SmtpTransport::class)
                ->setPublic(true);
        }
    }
}
