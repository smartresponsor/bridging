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
            $paths = [];
            $bridgeTemplateDir = realpath(__DIR__ . '/../../templates');
            if (false !== $bridgeTemplateDir) {
                $paths[$bridgeTemplateDir] = null;
            }

            $interfacingTemplateDir = realpath(__DIR__ . '/../../../Interfacing/template');
            if (false !== $interfacingTemplateDir) {
                $paths[$interfacingTemplateDir] = null;
            }

            $container->prependExtensionConfig('twig', [
                'paths' => $paths,
            ]);
        }
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $orderingContract = dirname(__DIR__) . '/../../Ordering/src/ServiceInterface/OrderSummaryProviderInterface.php';
        if (is_file($orderingContract)) {
            require_once $orderingContract;
        }

        $container->setParameter('bridge.defaults.strict_resolution', $config['defaults']['strict_resolution']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config/component'));
        $loader->load('services.yaml');
        foreach (glob(__DIR__ . '/../../config/component/services_*_interfacing.yaml') ?: [] as $path) {
            $loader->load(basename($path));
        }
    }

    public function getAlias(): string
    {
        return 'bridge';
    }
}
