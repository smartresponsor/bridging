<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use App\Bridging\Service\ExchangingInterfacing\ExchangeTemplateContextToInterfacingScreenBridge;
use PHPUnit\Framework\TestCase;

final class ExchangeTemplateContextToInterfacingScreenBridgeTest extends TestCase
{
    public function testBridgeConvertsExchangingTemplateContextIntoInterfacingScreenPayload(): void
    {
        $bridge = new ExchangeTemplateContextToInterfacingScreenBridge(new InterfacingScreenPayloadNormalizer());

        $payload = new class {
            /**
             * @return array<string, mixed>
             */
            public function toArray(): array
            {
                return [
                    'component' => 'Exchanging',
                    'viewType' => 'exchange_quote_summary',
                    'title' => 'USD/UAH exchange quote',
                    'money' => [
                        'baseCurrencyCode' => 'USD',
                        'quoteCurrencyCode' => 'UAH',
                        'baseAmount' => '100',
                        'quoteAmount' => '4125.00',
                    ],
                    'rate' => ['rateValue' => '41.2500000000', 'rateDate' => '2026-05-03'],
                    'provider' => ['providerCode' => 'nbu'],
                    'freshness' => ['status' => 'fresh'],
                    'audit' => ['appliedRateFingerprint' => 'exchange-rate-demo'],
                    'links' => ['latestRates' => '/exchanging/rates/latest'],
                ];
            }
        };

        self::assertTrue($bridge->supports($payload, BridgeTarget::SCREEN_EXCHANGING_TEMPLATE_CONTEXT));

        $screen = $bridge->bridge($payload, BridgeTarget::SCREEN_EXCHANGING_TEMPLATE_CONTEXT, ['source' => 'test']);

        self::assertSame('exchanging.exchange.quote.summary', $screen['id']);
        self::assertSame('summary', $screen['kind']);
        self::assertSame('USD/UAH exchange quote', $screen['title']);
        self::assertSame('test', $screen['bridgeContext']['source']);
        self::assertSame('Exchanging', $screen['meta']['component']);
        self::assertSame('USD', $screen['meta']['money']['baseCurrencyCode']);
        self::assertSame('nbu', $screen['meta']['provider']['providerCode']);
        self::assertCount(5, $screen['items']);
    }
}
