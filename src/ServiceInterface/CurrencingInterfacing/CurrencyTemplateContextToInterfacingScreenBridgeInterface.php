<?php

declare(strict_types=1);

namespace App\Bridging\ServiceInterface\CurrencingInterfacing;

use App\Bridging\Bridge\Contract\BridgeInterface;

interface CurrencyTemplateContextToInterfacingScreenBridgeInterface extends BridgeInterface
{
    /**
     * Converts a Currencing outbound template context object into the canonical Interfacing screen payload array.
     *
     * Bridging intentionally accepts object/shape typing here so this connector remains package-boundary safe and
     * does not require Currencing classes to be autoloadable when Bridging is tested independently.
     *
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function bridgeToScreen(object $payload, array $context = []): array;
}
