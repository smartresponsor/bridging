<?php

declare(strict_types=1);

namespace App\Bridging\ServiceInterface\ExchangingInterfacing;

use App\Bridging\Bridge\Contract\BridgeInterface;

interface ExchangeTemplateContextToInterfacingScreenBridgeInterface extends BridgeInterface
{
    /**
     * Converts an Exchanging outbound template context object into the canonical Interfacing screen payload array.
     *
     * Bridging intentionally accepts object/shape typing so this connector remains package-boundary safe and does
     * not require Exchanging classes to be autoloadable when Bridging is tested independently.
     *
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function bridgeToScreen(object $payload, array $context = []): array;
}
