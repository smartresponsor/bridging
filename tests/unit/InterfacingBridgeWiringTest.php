<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Accessing\ServiceInterface\Rendering\PageResponderInterface as AccessingPageResponderInterface;
use App\Bridging\DependencyInjection\BridgingExtension;
use App\Bridging\Service\CatalogingInterfacing\CatalogingCategoryApiClientBridge;
use App\Bridging\Service\CurrencingInterfacing\CurrencyTemplateContextToInterfacingScreenBridge;
use App\Bridging\Service\CrudingInterfacing\CrudPageToWorkbenchViewBridge;
use App\Bridging\Service\MessagingInterfacing\MessageInterfacingScreenBridge;
use App\Bridging\Service\OrderingInterfacing\OrderingOrderSummaryQueryBridge;
use App\Bridging\ServiceInterface\CrudingInterfacing\CrudPageToWorkbenchViewBridgeInterface;
use App\Bridging\ServiceInterface\CurrencingInterfacing\CurrencyTemplateContextToInterfacingScreenBridgeInterface;
use App\Bridging\ServiceInterface\MessagingInterfacing\MessageInterfacingScreenBridgeInterface;
use App\Interfacing\ServiceInterface\Interfacing\CategoryApiClientInterface;
use App\Interfacing\ServiceInterface\Interfacing\Query\OrderSummaryQueryServiceInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use PHPUnit\Framework\TestCase;

final class InterfacingBridgeWiringTest extends TestCase
{
    public function testInterfacingBridgeAliasesAreLoadedByBridgingExtension(): void
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

        self::assertTrue($container->hasAlias(AccessingPageResponderInterface::class));
        self::assertTrue($container->hasAlias(MessageInterfacingScreenBridgeInterface::class));
        self::assertTrue($container->hasAlias(CrudPageToWorkbenchViewBridgeInterface::class));
        self::assertTrue($container->hasAlias(CurrencyTemplateContextToInterfacingScreenBridgeInterface::class));
        self::assertTrue($container->hasAlias(CategoryApiClientInterface::class));
        self::assertTrue($container->hasAlias(OrderSummaryQueryServiceInterface::class));
        self::assertTrue($container->hasDefinition(MessageInterfacingScreenBridge::class));
        self::assertTrue($container->hasDefinition(CrudPageToWorkbenchViewBridge::class));
        self::assertTrue($container->hasDefinition(CurrencyTemplateContextToInterfacingScreenBridge::class));
        self::assertTrue($container->hasDefinition(CatalogingCategoryApiClientBridge::class));
        self::assertTrue($container->hasDefinition(OrderingOrderSummaryQueryBridge::class));
    }
}
