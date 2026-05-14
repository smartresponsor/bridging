<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use App\Bridging\Service\PagingInterfacing\PageBridgePayloadToInterfacingScreenBridge;
use PHPUnit\Framework\TestCase;

final class PageBridgePayloadToInterfacingScreenBridgeTest extends TestCase
{
    public function testBridgeConvertsPagingPageBridgePayloadIntoInterfacingScreenPayload(): void
    {
        $bridge = new PageBridgePayloadToInterfacingScreenBridge(new InterfacingScreenPayloadNormalizer());

        $payload = new class {
            /**
             * @return array<string, mixed>
             */
            public function toArray(): array
            {
                return [
                    'source' => 'paging.page_bridge_payload',
                    'code' => 'privacy_policy',
                    'slug' => 'privacy-policy',
                    'title' => 'Privacy Policy',
                    'kind' => 'policy',
                    'status' => 'published',
                    'publicationStatus' => 'published',
                    'revisionNumber' => 3,
                    'bodyHtml' => '<h1>Privacy Policy</h1>',
                    'bodyText' => 'Privacy Policy',
                    'checksum' => 'abc123',
                    'effectiveFrom' => '2026-05-10T00:00:00-05:00',
                    'renderHints' => ['showVersion' => true],
                    'legalNotice' => ['requiresAcceptance' => true],
                    'attachments' => [
                        ['attachmentId' => 'att-1', 'attachmentCode' => 'privacy-pdf', 'usage' => 'download', 'label' => 'PDF'],
                    ],
                ];
            }
        };

        self::assertTrue($bridge->supports($payload, BridgeTarget::PAGING_INTERFACING_SCREEN));

        $screen = $bridge->bridge($payload, BridgeTarget::PAGING_INTERFACING_SCREEN, ['source' => 'test']);

        self::assertSame('page.privacy_policy', $screen['id']);
        self::assertSame('document', $screen['kind']);
        self::assertSame('Privacy Policy', $screen['title']);
        self::assertSame('Paging · Interfacing', $screen['eyebrow']);
        self::assertSame('test', $screen['bridgeContext']['source']);
        self::assertSame('privacy-policy', $screen['context']['slug']);
        self::assertTrue($screen['context']['requiresAcceptance']);
        self::assertSame('<h1>Privacy Policy</h1>', $screen['meta']['bodyHtml']);
        self::assertSame('paging.page_bridge_payload', $screen['meta']['source']);
        self::assertCount(1, $screen['items']);
    }
}
