<?php

namespace App\DependencyInjection\Mailer;

use App\Service\Mail\Mailer\ChainMailer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MailTransportCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->findDefinition(ChainMailer::class);

        $taggedServices = $container->findTaggedServiceIds('mail.transport');

        foreach (array_keys($taggedServices) as $id) {
            $definition->addMethodCall('addTransport', [new Reference($id)]);
        }
    }
}
