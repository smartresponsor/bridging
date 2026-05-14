<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use App\Bridging\Service\SubscriptingInterfacing\SubscriptionPresentationToInterfacingScreenBridge;
use PHPUnit\Framework\TestCase;

final class SubscriptionPresentationToInterfacingScreenBridgeTest extends TestCase
{
    public function testBridgeConvertsSubscriptingPresentationPayloadIntoInterfacingScreenPayload(): void
    {
        $bridge = new SubscriptionPresentationToInterfacingScreenBridge(new InterfacingScreenPayloadNormalizer());

        $payload = new class {
            /**
             * @return array<string, mixed>
             */
            public function toArray(): array
            {
                return [
                    'component' => 'subscripting',
                    'contract' => 'subscription.presentation.v1',
                    'subjectType' => 'account',
                    'subjectId' => 'demo-account-1',
                    'state' => [
                        'subjectType' => 'account',
                        'subjectId' => 'demo-account-1',
                        'status' => 'active',
                        'planCode' => 'starter-monthly',
                        'planName' => 'Starter Monthly',
                    ],
                    'planOptions' => [
                        [
                            'code' => 'starter-monthly',
                            'name' => 'Starter Monthly',
                            'periodCount' => 1,
                            'periodUnit' => 'month',
                            'trialDays' => 7,
                            'currencyCode' => 'USD',
                            'amountMinorSnapshot' => 1900,
                        ],
                    ],
                    'entitlements' => [
                        [
                            'code' => 'feature.basic_dashboard',
                            'granted' => true,
                        ],
                    ],
                    'actions' => [
                        [
                            'code' => 'subscription.manage',
                            'label' => 'Manage subscription',
                            'intent' => 'subscription.command.manage',
                        ],
                    ],
                    'slots' => [
                        'subscription.current_state' => ['shape' => 'SubscriptionPresentationStateDto'],
                    ],
                ];
            }
        };

        self::assertTrue($bridge->supports($payload, BridgeTarget::SCREEN_SUBSCRIPTING_PRESENTATION));

        $screen = $bridge->bridge($payload, BridgeTarget::SCREEN_SUBSCRIPTING_PRESENTATION, ['source' => 'test']);

        self::assertSame('subscripting.account.demo-account-1.presentation', $screen['id']);
        self::assertSame('dashboard', $screen['kind']);
        self::assertSame('Subscription', $screen['title']);
        self::assertSame('Subscripting · Interfacing', $screen['eyebrow']);
        self::assertSame('test', $screen['bridgeContext']['source']);
        self::assertSame('subscription.presentation.v1', $screen['meta']['contract']);
        self::assertSame('starter-monthly', $screen['meta']['planOptions'][0]['code']);
        self::assertSame('feature.basic_dashboard', $screen['meta']['entitlements'][0]['code']);
        self::assertSame('subscription.command.manage', $screen['primaryActions'][0]['intent']);
    }

    public function testBridgeRejectsUnsupportedTarget(): void
    {
        $bridge = new SubscriptionPresentationToInterfacingScreenBridge(new InterfacingScreenPayloadNormalizer());

        $payload = new class {
            /**
             * @return array<string, mixed>
             */
            public function toArray(): array
            {
                return [
                    'component' => 'subscripting',
                    'contract' => 'subscription.presentation.v1',
                ];
            }
        };

        self::assertFalse($bridge->supports($payload, BridgeTarget::SCREEN_CURRENCING_TEMPLATE_CONTEXT));
    }
}
