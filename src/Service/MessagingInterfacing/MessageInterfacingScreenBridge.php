<?php

declare(strict_types=1);

namespace App\Bridging\Service\MessagingInterfacing;

use App\Bridging\Bridge\Contract\BridgeInterface;
use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\ServiceInterface\MessagingInterfacing\MessageInterfacingScreenBridgeInterface;
use App\Value\Interfacing\MessageInterfacingScreenContractInterface;
use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('bridging.dispatch_bridge')]
final readonly class MessageInterfacingScreenBridge implements MessageInterfacingScreenBridgeInterface
{
    public function __construct(
        private InterfacingScreenPayloadNormalizer $payloadNormalizer,
    ) {
    }

    public function supports(object $payload, string $target): bool
    {
        return $payload instanceof MessageInterfacingScreenContractInterface && BridgeTarget::SCREEN_MESSAGE === $target;
    }

    /**
     * @return array<string, mixed>
     */
    public function bridge(object $payload, string $target, array $context = []): array
    {
        if (!$payload instanceof MessageInterfacingScreenContractInterface || !$this->supports($payload, $target)) {
            throw new \InvalidArgumentException('Unsupported bridge payload for Messaging → Interfacing screen bridge.');
        }

        return $this->bridgeToScreen($payload, $context);
    }

    public function bridgeToScreen(MessageInterfacingScreenContractInterface $payload, array $context = []): array
    {
        return $this->payloadNormalizer->normalize($payload->toInterfacingScreenPayload(), $context);
    }
}
