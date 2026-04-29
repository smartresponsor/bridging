<?php

declare(strict_types=1);

namespace App\Bridging\Bridge;

final class BridgeManager
{
    public function __construct(
        private readonly BridgeRegistry $registry,
        private readonly bool $strictResolution = true,
    ) {
    }

    public function bridge(object $payload, string $target, array $context = []): mixed
    {
        foreach ($this->registry->all() as $bridge) {
            if ($bridge->supports($payload, $target)) {
                return $bridge->bridge($payload, $target, $context);
            }
        }

        if ($this->strictResolution) {
            throw new \RuntimeException(sprintf(
                'No bridge supports payload "%s" for target "%s".',
                $payload::class,
                $target,
            ));
        }

        return null;
    }
}
