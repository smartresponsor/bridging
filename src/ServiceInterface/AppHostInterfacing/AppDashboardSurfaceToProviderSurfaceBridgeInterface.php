<?php

declare(strict_types=1);

namespace App\Bridging\ServiceInterface\AppHostInterfacing;

use App\Bridging\Bridge\Contract\BridgeInterface;

/**
 * Bridge contract for App-owned dashboard surfaces that must be rendered by
 * Interfacing provider documents rather than by host handmade Twig/CSS.
 */
interface AppDashboardSurfaceToProviderSurfaceBridgeInterface extends BridgeInterface
{
    /**
     * Expected primary payload: App\Dto\Dashboard\AppDashboardSurfacePayload.
     *
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function bridgeToProviderSurface(object $payload, array $context = []): array;
}
