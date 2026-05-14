<?php

declare(strict_types=1);

namespace App\Bridging\Controller\Interfacing;

use App\Bridging\Bridge\Contract\BridgeVisibleProviderSurfaceRouteContract;
use App\Interfacing\ServiceInterface\Interfacing\Presentation\InterfacingRendererInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bridge-facing visible route adopter for e-commerce/admin component pages.
 *
 * This controller intentionally does not render tables, forms, local styling,
 * or handmade Twig UI. It only maps visible route/resource context to the
 * Interfacing provider surface so Ant Design ProComponents and PrimeReact own
 * the actual admin/workbench body.
 */
final readonly class BridgeVisibleProviderSurfaceController
{
    public function __construct(
        private InterfacingRendererInterface $renderer,
    ) {}

    public function show(Request $request, string $prefix, ?string $resourcePath = null): Response
    {
        $normalizedPrefix = $this->normalizeSegment($prefix, 'catalog');
        $normalizedResourcePath = $this->normalizeResourcePath($normalizedPrefix, $resourcePath);
        $operation = $this->resolveOperation($request, $normalizedResourcePath);
        $component = BridgeVisibleProviderSurfaceRouteContract::PREFIX_COMPONENT_MAP[$normalizedPrefix] ?? $this->inferComponent($normalizedResourcePath);
        $title = $this->resolveTitle($request, $normalizedResourcePath);

        return $this->renderer->render(BridgeVisibleProviderSurfaceRouteContract::TEMPLATE, [
            'bridgeComponent' => $component,
            'bridgeResource' => $normalizedResourcePath,
            'bridgeOperation' => $operation,
            'bridgeSurface' => 'admin',
            'bridgeTitle' => $title,
            'bridgeCollectionLabel' => $title,
            'bridgeRows' => [],
        ]);
    }

    private function normalizeResourcePath(string $prefix, ?string $resourcePath): string
    {
        $candidate = $resourcePath;
        if ($candidate === null || trim($candidate) === '') {
            $candidate = $prefix;
        }

        $candidate = strtolower(str_replace(['_', '\\'], ['-', '/'], trim($candidate, '/ ')));
        $candidate = preg_replace('/[^a-z0-9\/_-]+/', '-', $candidate) ?: $prefix;
        $candidate = preg_replace('#/+#', '/', $candidate) ?: $prefix;
        $candidate = trim($candidate, '/');

        return $candidate !== '' ? $candidate : $prefix;
    }

    private function normalizeSegment(string $segment, string $fallback): string
    {
        $segment = strtolower(trim($segment, '/ '));
        $segment = preg_replace('/[^a-z0-9_-]+/', '-', $segment) ?: $fallback;

        return $segment !== '' ? $segment : $fallback;
    }

    private function resolveOperation(Request $request, string $resourcePath): string
    {
        $queryOperation = $request->query->get('operation');
        if (is_string($queryOperation) && trim($queryOperation) !== '') {
            return $this->normalizeSegment($queryOperation, 'index');
        }

        $last = basename($resourcePath);

        return match ($last) {
            'new', 'create' => 'new',
            'edit', 'update' => 'edit',
            'show', 'view', 'detail' => 'show',
            'delete', 'remove' => 'delete',
            default => 'index',
        };
    }

    private function resolveTitle(Request $request, string $resourcePath): string
    {
        $queryTitle = $request->query->get('title');
        if (is_string($queryTitle) && trim($queryTitle) !== '') {
            return trim($queryTitle);
        }

        $text = str_replace(['-', '_', '/'], ' ', $resourcePath);
        $text = preg_replace('/\s+/', ' ', $text) ?: $resourcePath;

        return ucwords(trim($text));
    }

    private function inferComponent(string $resourcePath): string
    {
        $first = strtolower(strtok(str_replace('_', '/', $resourcePath), '/') ?: $resourcePath);

        return match ($first) {
            'catalog', 'category', 'product' => 'cataloging',
            'crud', 'cruding', 'relation', 'object' => 'cruding',
            'vendor', 'vendoring', 'merchant' => 'vendoring',
            default => 'bridge',
        };
    }
}
