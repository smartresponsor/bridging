<?php

declare(strict_types=1);

namespace App\Bridging\Service\AccessingInterfacing;

use App\Accessing\Dto\PageView;
use App\Bridging\Bridge\Contract\BridgeInterface;
use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('bridging.bridge')]
final readonly class AccessingPageToInterfacingScreenBridge implements BridgeInterface
{
    public function __construct(
        private AccessingInterfacingScreenSpecProvider $screenSpecProvider,
        private InterfacingScreenPayloadNormalizer $payloadNormalizer,
    ) {
    }

    public function supports(object $payload, string $target): bool
    {
        return $payload instanceof PageView && BridgeTarget::ACCESSING_INTERFACING_SCREEN === $target;
    }

    /**
     * @return array<string, mixed>
     */
    public function bridge(object $payload, string $target, array $context = []): array
    {
        if (!$payload instanceof PageView || !$this->supports($payload, $target)) {
            throw new \InvalidArgumentException('Unsupported bridge payload for Accessing → Interfacing screen bridge.');
        }

        return $this->payloadNormalizer->normalize($this->screenSpecProvider->resolve($payload, $context), $context);
    }
}
