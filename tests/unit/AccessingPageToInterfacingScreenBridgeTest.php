<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Accessing\Dto\PageView;
use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use App\Bridging\Service\AccessingInterfacing\AccessingInterfacingScreenSpecProvider;
use App\Bridging\Service\AccessingInterfacing\AccessingPageToInterfacingScreenBridge;
use PHPUnit\Framework\TestCase;

final class AccessingPageToInterfacingScreenBridgeTest extends TestCase
{
    public function testBridgeConvertsAccessingPageViewIntoInterfacingScreenPayload(): void
    {
        $bridge = new AccessingPageToInterfacingScreenBridge(
            new AccessingInterfacingScreenSpecProvider(),
            new InterfacingScreenPayloadNormalizer(),
        );

        $pageView = new PageView('account.overview', ['foo' => 'bar']);

        self::assertTrue($bridge->supports($pageView, BridgeTarget::ACCESSING_INTERFACING_SCREEN));

        $screen = $bridge->bridge($pageView, BridgeTarget::ACCESSING_INTERFACING_SCREEN, ['source' => 'test']);

        self::assertSame('account.overview', $screen['id']);
        self::assertSame('overview', $screen['kind']);
        self::assertSame('test', $screen['bridgeContext']['source']);
        self::assertSame('bar', $screen['context']['foo']);
    }
}
