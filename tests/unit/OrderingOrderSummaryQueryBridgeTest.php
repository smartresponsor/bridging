<?php

declare(strict_types=1);

namespace App\Bridging\Tests\Unit;

use App\Bridging\Service\OrderingInterfacing\OrderingOrderSummaryQueryBridge;
use App\Interfacing\Contract\Dto\OrderSummaryRow;
use App\Interfacing\ServiceInterface\Interfacing\Query\OrderSummaryQueryServiceInterface;
use App\Ordering\ServiceInterface\OrderSummaryProviderInterface;
use PHPUnit\Framework\TestCase;

final class OrderingOrderSummaryQueryBridgeTest extends TestCase
{
    public function testBridgeMapsOrderingSummaryPayloadIntoInterfacingPage(): void
    {
        $provider = new class() implements OrderSummaryProviderInterface {
            public function fetchPage(string $tenantId, int $page, int $pageSize, ?string $status, ?string $createdFromIso, ?string $createdToIso): array
            {
                return [
                    'item' => [
                        [
                            'id' => 'ord-1',
                            'status' => 'paid',
                            'createdAtIso' => '2025-01-10T12:00:00+00:00',
                            'totalGross' => 199.99,
                            'currencyCode' => 'USD',
                            'customerEmail' => null,
                        ],
                    ],
                    'total' => 1,
                    'page' => $page,
                    'pageSize' => $pageSize,
                ];
            }
        };

        $bridge = new OrderingOrderSummaryQueryBridge($provider);
        $page = $bridge->fetchPage('tenant-1', 2, 25, 'paid', null, null);

        self::assertCount(1, $page->items);
        self::assertInstanceOf(OrderSummaryRow::class, $page->items[0]);
        self::assertSame('ord-1', $page->items[0]->id);
        self::assertSame(2, $page->page);
        self::assertSame(25, $page->pageSize);
    }
}
