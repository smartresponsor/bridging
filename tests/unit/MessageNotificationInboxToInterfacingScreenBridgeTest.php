<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Service\MessagingInterfacing\MessageNotificationInboxToInterfacingScreenBridge;
use App\Value\Notification\MessageNotificationInboxValue;
use PHPUnit\Framework\TestCase;

final class MessageNotificationInboxToInterfacingScreenBridgeTest extends TestCase
{
    public function testBridgeConvertsInboxValueIntoInterfacingScreenPayload(): void
    {
        $bridge = new MessageNotificationInboxToInterfacingScreenBridge(new InterfacingScreenPayloadNormalizer());
        $inbox = new MessageNotificationInboxValue('user-1', [
            ['id' => 'notif-1', 'title' => 'Welcome'],
        ], 25);

        self::assertTrue($bridge->supports($inbox, BridgeTarget::MESSAGING_INTERFACING_NOTIFICATION_INBOX_SCREEN));
        $screen = $bridge->bridge($inbox, BridgeTarget::MESSAGING_INTERFACING_NOTIFICATION_INBOX_SCREEN, ['source' => 'test']);
        self::assertSame('message.notifications.inbox', $screen['id']);
        self::assertSame(1, $screen['itemCount']);
        self::assertSame('test', $screen['bridgeContext']['source']);
    }
}
