<?php

declare(strict_types=1);

namespace App\Bridging\Service\MessagingInterfacing;

use App\Bridging\Bridge\Contract\BridgeInterface;
use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Value\Search\MessageSearchResultsValue;
use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('bridging.dispatch_bridge')]
final readonly class MessageSearchResultsToInterfacingScreenBridge implements BridgeInterface
{
    public function __construct(
        private InterfacingScreenPayloadNormalizer $payloadNormalizer,
    ) {
    }

    public function supports(object $payload, string $target): bool
    {
        return $payload instanceof MessageSearchResultsValue && BridgeTarget::SCREEN_MESSAGE_SEARCH_RESULTS === $target;
    }

    /**
     * @return array<string, mixed>
     */
    public function bridge(object $payload, string $target, array $context = []): array
    {
        if (!$payload instanceof MessageSearchResultsValue || !$this->supports($payload, $target)) {
            throw new \InvalidArgumentException('Unsupported bridge payload for Messaging → Interfacing search bridge.');
        }

        return $this->payloadNormalizer->normalize($payload->toInterfacingScreenPayload(), $context);
    }
}
