<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use App\Bridging\Service\CurrencingInterfacing\CurrencyTemplateContextToInterfacingScreenBridge;
use PHPUnit\Framework\TestCase;

final class CurrencyTemplateContextToInterfacingScreenBridgeTest extends TestCase
{
    public function testBridgeConvertsCurrencingTemplateContextIntoInterfacingScreenPayload(): void
    {
        $bridge = new CurrencyTemplateContextToInterfacingScreenBridge(new InterfacingScreenPayloadNormalizer());

        $payload = new class() {
            /**
             * @return array<string, mixed>
             */
            public function toArray(): array
            {
                return [
                    'componentKey' => 'currencing',
                    'selectedCurrencyCode' => 'USD',
                    'locale' => 'en',
                    'selector' => [
                        'options' => [
                            ['code' => 'USD', 'label' => 'US Dollar', 'symbol' => '$', 'minorUnit' => 2, 'selected' => true],
                            ['code' => 'EUR', 'label' => 'Euro', 'symbol' => '€', 'minorUnit' => 2, 'selected' => false],
                        ],
                    ],
                    'metadata' => [],
                    'routeNames' => ['catalog' => 'currencing_currency_catalog'],
                    'capabilities' => ['selector' => true],
                ];
            }
        };

        self::assertTrue($bridge->supports($payload, BridgeTarget::SCREEN_CURRENCING_TEMPLATE_CONTEXT));

        $screen = $bridge->bridge($payload, BridgeTarget::SCREEN_CURRENCING_TEMPLATE_CONTEXT, ['source' => 'test']);

        self::assertSame('currencing.currency.selector', $screen['id']);
        self::assertSame('collection', $screen['kind']);
        self::assertSame('Currency selector', $screen['title']);
        self::assertSame('test', $screen['bridgeContext']['source']);
        self::assertCount(2, $screen['items']);
        self::assertSame('USD', $screen['items'][0]['meta']['code']);
    }
}
