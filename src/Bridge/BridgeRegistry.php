<?php

declare(strict_types=1);

namespace App\Bridging\Bridge;

use App\Bridging\Bridge\Contract\BridgeInterface;

final class BridgeRegistry
{
    /**
     * @param iterable<BridgeInterface> $bridges
     */
    public function __construct(
        private readonly iterable $bridges,
    ) {
    }

    /**
     * @return list<BridgeInterface>
     */
    public function all(): array
    {
        return array_values(is_array($this->bridges) ? $this->bridges : iterator_to_array($this->bridges, false));
    }
}