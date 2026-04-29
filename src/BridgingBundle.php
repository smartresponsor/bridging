<?php

declare(strict_types=1);

namespace App\Bridging;

use App\Bridging\DependencyInjection\BridgingExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class BridgingBundle extends Bundle
{
    public function getContainerExtension(): BridgingExtension
    {
        if (null === $this->extension) {
            $this->extension = new BridgingExtension();
        }

        return $this->extension;
    }
}