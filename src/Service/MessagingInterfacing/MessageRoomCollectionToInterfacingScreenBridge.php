<?php

declare(strict_types=1);

namespace App\Bridging\Service\MessagingInterfacing;

use App\Bridging\Bridge\Contract\BridgeInterface;
use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Value\Room\MessageRoomCollectionValue;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('bridging.bridge')]
final readonly class MessageRoomCollectionToInterfacingScreenBridge implements BridgeInterface
{
    public function supports(object $payload, string $target): bool
    {
        return $payload instanceof MessageRoomCollectionValue && BridgeTarget::MESSAGING_INTERFACING_ROOM_COLLECTION_SCREEN === $target;
    }

    /**
     * @return array<string, mixed>
     */
    public function bridge(object $payload, string $target, array $context = []): array
    {
        if (!$payload instanceof MessageRoomCollectionValue || !$this->supports($payload, $target)) {
            throw new \InvalidArgumentException('Unsupported bridge payload for Messaging → Interfacing room collection bridge.');
        }

        return $payload->toInterfacingScreenPayload() + [
            'bridgeContext' => $context,
        ];
    }
}
