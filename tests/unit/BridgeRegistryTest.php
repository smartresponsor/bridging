<?php

declare(strict_types=1);

namespace App\Bridging\Bridge\Tests\Unit;

use App\Bridging\Bridge\BridgeRegistry;
use App\Bridging\Bridge\Contract\BridgeInterface;
use PHPUnit\Framework\TestCase;

final class BridgeRegistryTest extends TestCase
{
    public function testAllReturnsRegisteredBridges(): void
    {
        $bridge = new class() implements BridgeInterface {
            public function supports(object $payload, string $target): bool
            {
                return true;
            }

            public function bridge(object $payload, string $target, array $context = []): mixed
            {
                return $payload;
            }
        };

        $registry = new BridgeRegistry([$bridge]);

        self::assertSame([$bridge], $registry->all());
    }
}
