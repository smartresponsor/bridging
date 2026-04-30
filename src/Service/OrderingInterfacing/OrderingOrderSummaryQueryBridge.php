<?php

declare(strict_types=1);

namespace App\Bridging\Service\OrderingInterfacing;

use App\Interfacing\Contract\Dto\OrderSummaryPage;
use App\Interfacing\Contract\Dto\OrderSummaryRow;
use App\Interfacing\ServiceInterface\Interfacing\Query\OrderSummaryQueryServiceInterface;
use App\Ordering\ServiceInterface\OrderSummaryProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('bridging.integration_adapter')]
final readonly class OrderingOrderSummaryQueryBridge implements OrderSummaryQueryServiceInterface
{
    public function __construct(
        private OrderSummaryProviderInterface $orderSummaryProvider,
    ) {
    }

    public function fetchPage(
        string $tenantId,
        int $page,
        int $pageSize,
        ?string $status,
        ?string $createdFromIso,
        ?string $createdToIso,
    ): OrderSummaryPage {
        $result = $this->orderSummaryProvider->fetchPage($tenantId, $page, $pageSize, $status, $createdFromIso, $createdToIso);
        $items = [];
        foreach (($result['item'] ?? []) as $row) {
            if (!is_array($row)) {
                continue;
            }

            $items[] = new OrderSummaryRow(
                (string) ($row['id'] ?? ''),
                (string) ($row['status'] ?? ''),
                (string) ($row['createdAtIso'] ?? ''),
                (float) ($row['totalGross'] ?? 0.0),
                (string) ($row['currencyCode'] ?? 'USD'),
                isset($row['customerEmail']) ? (null === $row['customerEmail'] ? null : (string) $row['customerEmail']) : null,
            );
        }

        return new OrderSummaryPage(
            $items,
            (int) ($result['total'] ?? count($items)),
            (int) ($result['page'] ?? $page),
            (int) ($result['pageSize'] ?? $pageSize),
        );
    }
}
