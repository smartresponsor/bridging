<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Bridging\Service\ExchangingInterfacing\ExchangeTemplateContextToInterfacingScreenBridge;
use App\Bridging\ServiceInterface\ExchangingInterfacing\ExchangeTemplateContextToInterfacingScreenBridgeInterface;
use App\Bridging\Tests\Support\TestKernel;
use PHPUnit\Framework\TestCase;

final class ExchangingInterfacingBridgeWiringTest extends TestCase
{
    public function testExchangingInterfacingBridgeAliasIsWired(): void
    {
        $kernel = new TestKernel('test', true);
        $kernel->boot();

        try {
            $container = $kernel->getContainer();
            $service = $container->get(ExchangeTemplateContextToInterfacingScreenBridgeInterface::class);

            self::assertInstanceOf(ExchangeTemplateContextToInterfacingScreenBridge::class, $service);
        } finally {
            $kernel->shutdown();
        }
    }
}
