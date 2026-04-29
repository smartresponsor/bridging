<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Service\MessagingInterfacing\MessageDigestToInterfacingScreenBridge;
use App\Value\Thread\MessageThreadDigestValue;
use PHPUnit\Framework\TestCase;

final class MessageDigestToInterfacingScreenBridgeTest extends TestCase
{
    public function testBridgeConvertsDigestValueIntoInterfacingScreenPayload(): void
    {
        $bridge = new MessageDigestToInterfacingScreenBridge(new InterfacingScreenPayloadNormalizer());
        $digest = new MessageThreadDigestValue('Daily digest', 'Unread threads summary', [
            ['threadId' => 'thread-1', 'unread' => 3, 'lastMessageAt' => '2026-04-28 10:00:00'],
        ]);

        self::assertTrue($bridge->supports($digest, BridgeTarget::MESSAGING_INTERFACING_DIGEST_SCREEN));
        $screen = $bridge->bridge($digest, BridgeTarget::MESSAGING_INTERFACING_DIGEST_SCREEN);
        self::assertSame('message.digest', $screen['id']);
        self::assertSame(1, $screen['itemCount']);
    }
}
