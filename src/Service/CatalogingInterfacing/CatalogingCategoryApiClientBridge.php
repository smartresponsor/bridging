<?php

declare(strict_types=1);

namespace App\Bridging\Service\CatalogingInterfacing;

use App\Cataloging\ServiceInterface\CatalogCategoryAdminServiceInterface;
use App\Interfacing\Contract\Dto\CategoryItemView;
use App\Interfacing\ServiceInterface\Interfacing\CategoryApiClientInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('bridging.integration_adapter')]
final readonly class CatalogingCategoryApiClientBridge implements CategoryApiClientInterface
{
    public function __construct(
        private CatalogCategoryAdminServiceInterface $catalogCategoryAdminService,
    ) {
    }

    public function list(string $query, ?string $cursor, int $limit): array
    {
        $result = $this->catalogCategoryAdminService->list($query, $cursor, $limit);
        $items = [];
        foreach (($result['item'] ?? []) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $items[] = new CategoryItemView(
                (string) ($row['id'] ?? ''),
                (string) ($row['slug'] ?? ''),
                (string) ($row['name'] ?? ''),
                (string) ($row['locale'] ?? 'en'),
                (string) ($row['status'] ?? 'active'),
            );
        }

        return ['item' => $items, 'nextCursor' => isset($result['nextCursor']) ? (string) $result['nextCursor'] : null];
    }

    public function read(string $id): array
    {
        return $this->catalogCategoryAdminService->read($id);
    }

    public function save(string $id, array $payload): array
    {
        return $this->catalogCategoryAdminService->save($id, $payload);
    }
}
