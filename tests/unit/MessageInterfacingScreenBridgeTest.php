<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Service\MessagingInterfacing\MessageInterfacingScreenBridge;
use App\Value\Export\MessageExportJobValue;
use PHPUnit\Framework\TestCase;

final class MessageInterfacingScreenBridgeTest extends TestCase
{
    public function testBridgeConvertsMessagingContractsIntoInterfacingScreenPayload(): void
    {
        $bridge = new MessageInterfacingScreenBridge();
        $payload = new MessageExportJobValue('job-1', 'PENDING');

        self::assertTrue($bridge->supports($payload, BridgeTarget::MESSAGING_INTERFACING_SCREEN));
        $screen = $bridge->bridge($payload, BridgeTarget::MESSAGING_INTERFACING_SCREEN, ['source' => 'test']);
        self::assertSame('message.export', $screen['id']);
        self::assertSame('test', $screen['bridgeContext']['source']);
    }
}
