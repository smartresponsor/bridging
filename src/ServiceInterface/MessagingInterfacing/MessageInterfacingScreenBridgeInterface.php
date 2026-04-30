<?php

declare(strict_types=1);

namespace App\Bridging\ServiceInterface\MessagingInterfacing;

use App\Bridging\Bridge\Contract\BridgeInterface;
use App\Value\Interfacing\MessageInterfacingScreenContractInterface;

interface MessageInterfacingScreenBridgeInterface extends BridgeInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function bridgeToScreen(MessageInterfacingScreenContractInterface $payload, array $context = []): array;
}
