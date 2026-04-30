<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Accessing\ServiceInterface\Rendering\PageResponderInterface;
use App\Bridging\DependencyInjection\BridgingExtension;
use App\Bridging\Service\AccessingInterfacing\AccessingInterfacingPageResponder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use PHPUnit\Framework\TestCase;

final class AccessingInterfacingBridgeWiringTest extends TestCase
{
    public function testAccessingPageResponderAliasIsLoadedByBridgingExtension(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.project_dir', dirname(__DIR__, 3));

        $extension = new BridgingExtension();
        $extension->load([
            [
                'defaults' => [
                    'strict_resolution' => true,
                ],
            ],
        ], $container);

        self::assertTrue($container->hasAlias(PageResponderInterface::class));
        self::assertTrue($container->hasDefinition(AccessingInterfacingPageResponder::class));
    }
}
