<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Bridging\Bridge\BridgeManager;
use App\Bridging\Bridge\BridgeRegistry;
use App\Bridging\Bridge\Contract\BridgeInterface;
use PHPUnit\Framework\TestCase;

final class BridgeManagerTest extends TestCase
{
    public function testBridgeDelegatesToSupportingBridge(): void
    {
        $payload = new class() {
        };

        $bridge = new class() implements BridgeInterface {
            public function supports(object $payload, string $target): bool
            {
                return 'target' === $target;
            }

            public function bridge(object $payload, string $target, array $context = []): mixed
            {
                return ['target' => $target, 'ok' => true];
            }
        };

        $manager = new BridgeManager(new BridgeRegistry([$bridge]));

        self::assertSame(['target' => 'target', 'ok' => true], $manager->bridge($payload, 'target'));
    }
}
