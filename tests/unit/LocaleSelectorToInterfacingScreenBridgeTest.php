<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use App\Bridging\Service\LocalizingInterfacing\LocaleSelectorToInterfacingScreenBridge;
use App\Localizing\ValueObject\LocaleTemplateContext;
use App\Localizing\ValueObject\LocaleTemplateSelectorOption;
use PHPUnit\Framework\TestCase;

final class LocaleSelectorToInterfacingScreenBridgeTest extends TestCase
{
    public function testBridgeConvertsLocaleTemplateContextIntoInterfacingScreenPayload(): void
    {
        $bridge = new LocaleSelectorToInterfacingScreenBridge(new InterfacingScreenPayloadNormalizer());
        $payload = new LocaleTemplateContext(
            'uk',
            'en',
            [
                new LocaleTemplateSelectorOption('en', 'English', 'English', false, true),
                new LocaleTemplateSelectorOption('uk', 'Ukrainian', 'Українська', true, false),
            ],
            ['uk', 'en'],
            ['messages'],
            ['messages' => ['uk' => ['hello' => 'Привіт']]],
        );

        self::assertTrue($bridge->supports($payload, BridgeTarget::LOCALIZING_INTERFACING_SCREEN));

        $screen = $bridge->bridge($payload, BridgeTarget::LOCALIZING_INTERFACING_SCREEN, ['source' => 'test']);

        self::assertSame('localizing.locale.selector', $screen['id']);
        self::assertSame('collection', $screen['kind']);
        self::assertSame('Locale selector', $screen['title']);
        self::assertCount(2, $screen['items']);
        self::assertSame('test', $screen['bridgeContext']['source']);
    }
}
