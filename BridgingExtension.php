<?php

declare(strict_types=1);

namespace App\Bridging\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class BridgingExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('twig')) {
            $container->prependExtensionConfig('twig', [
                'paths' => [
                    realpath(__DIR__.'/../../templates') => null,
                ],
            ]);
        }
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('bridge.defaults.strict_resolution', $config['defaults']['strict_resolution']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config/component'));
        $loader->load('services.yaml');
    }

    public function getAlias(): string
    {
        return 'bridge';
    }
}