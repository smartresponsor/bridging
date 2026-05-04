<?php

declare(strict_types=1);

namespace App\Bridging\ServiceInterface\LocalizingInterfacing;

use App\Bridging\Bridge\Contract\BridgeInterface;
use App\Localizing\ValueObject\LocaleTemplateContext;

interface LocaleSelectorToInterfacingScreenBridgeInterface extends BridgeInterface
{
    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function bridgeToScreen(LocaleTemplateContext $payload, array $context = []): array;
}
