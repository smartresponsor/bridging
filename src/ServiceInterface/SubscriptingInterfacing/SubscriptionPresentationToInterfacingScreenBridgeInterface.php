<?php

declare(strict_types=1);

namespace App\Bridging\ServiceInterface\SubscriptingInterfacing;

use App\Bridging\Bridge\Contract\BridgeInterface;

interface SubscriptionPresentationToInterfacingScreenBridgeInterface extends BridgeInterface
{
    /**
     * Converts a Subscripting outbound presentation payload into the canonical Interfacing screen payload array.
     *
     * Bridging intentionally accepts object/shape typing so this connector remains package-boundary safe and does not
     * require Subscripting classes to be autoloadable when Bridging is tested independently.
     *
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function bridgeToScreen(object $payload, array $context = []): array;
}
