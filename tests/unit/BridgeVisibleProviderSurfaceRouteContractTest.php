<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Bridging\Bridge\Contract\BridgeVisibleProviderSurfaceRouteContract;
use PHPUnit\Framework\TestCase;

final class BridgeVisibleProviderSurfaceRouteContractTest extends TestCase
{
    public function testVisibleProviderRouteAdoptionContractLocksProviderCanon(): void
    {
        self::assertSame('interfacing/bridge/provider_surface.html.twig', BridgeVisibleProviderSurfaceRouteContract::TEMPLATE);
        self::assertSame('antd-pro', BridgeVisibleProviderSurfaceRouteContract::PRIMARY_PROVIDER);
        self::assertSame('primereact', BridgeVisibleProviderSurfaceRouteContract::SECONDARY_PROVIDER);
        self::assertContains('catalog', BridgeVisibleProviderSurfaceRouteContract::CANONICAL_VISIBLE_PREFIXES);
        self::assertContains('cruding', BridgeVisibleProviderSurfaceRouteContract::CANONICAL_VISIBLE_PREFIXES);
        self::assertContains('vendor', BridgeVisibleProviderSurfaceRouteContract::CANONICAL_VISIBLE_PREFIXES);
        self::assertSame('cataloging', BridgeVisibleProviderSurfaceRouteContract::PREFIX_COMPONENT_MAP['catalog']);
        self::assertSame('vendoring', BridgeVisibleProviderSurfaceRouteContract::PREFIX_COMPONENT_MAP['vendor']);
    }
}
