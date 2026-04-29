<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Service\MessagingInterfacing\MessageSearchResultsToInterfacingScreenBridge;
use App\Value\Search\MessageSearchResultsValue;
use PHPUnit\Framework\TestCase;

final class MessageSearchResultsToInterfacingScreenBridgeTest extends TestCase
{
    public function testBridgeConvertsSearchResultsIntoInterfacingScreenPayload(): void
    {
        $bridge = new MessageSearchResultsToInterfacingScreenBridge(new InterfacingScreenPayloadNormalizer());
        $results = new MessageSearchResultsValue('hello', null, null, 'user-1', 20, 0, [
            ['messageId' => 'msg-1', 'snippet' => 'Hello world'],
        ]);

        self::assertTrue($bridge->supports($results, BridgeTarget::MESSAGING_INTERFACING_SEARCH_RESULTS_SCREEN));
        $screen = $bridge->bridge($results, BridgeTarget::MESSAGING_INTERFACING_SEARCH_RESULTS_SCREEN);
        self::assertSame('message.search', $screen['id']);
        self::assertSame('hello', $screen['subtitle']);
        self::assertSame(1, $screen['collection']['items'] ? count($screen['collection']['items']) : 0);
    }
}
