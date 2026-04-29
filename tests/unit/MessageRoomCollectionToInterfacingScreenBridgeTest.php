<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Service\MessagingInterfacing\MessageRoomCollectionToInterfacingScreenBridge;
use App\Value\Room\MessageRoomCollectionValue;
use PHPUnit\Framework\TestCase;

final class MessageRoomCollectionToInterfacingScreenBridgeTest extends TestCase
{
    public function testBridgeConvertsRoomCollectionIntoInterfacingScreenPayload(): void
    {
        $bridge = new MessageRoomCollectionToInterfacingScreenBridge();
        $rooms = new MessageRoomCollectionValue([
            ['id' => 'room-1', 'title' => 'General'],
        ], 1, 50, 0, 'tenant-1');

        self::assertTrue($bridge->supports($rooms, BridgeTarget::MESSAGING_INTERFACING_ROOM_COLLECTION_SCREEN));
        $screen = $bridge->bridge($rooms, BridgeTarget::MESSAGING_INTERFACING_ROOM_COLLECTION_SCREEN, ['source' => 'test']);
        self::assertSame('message.rooms', $screen['id']);
        self::assertSame(1, $screen['collection']['count']);
        self::assertSame('tenant-1', $screen['collection']['tenantId']);
    }
}
