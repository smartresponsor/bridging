<?php

declare(strict_types=1);

namespace App\Bridging\Bridge\Contract;

interface BridgeInterface
{
    public function supports(object $payload, string $target): bool;

    public function bridge(object $payload, string $target, array $context = []): mixed;
}