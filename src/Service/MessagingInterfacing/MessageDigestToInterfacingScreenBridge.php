<?php

declare(strict_types=1);

namespace App\Bridging\Service\MessagingInterfacing;

use App\Bridging\Bridge\Contract\BridgeInterface;
use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Value\Thread\MessageThreadDigestValue;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('bridging.bridge')]
final readonly class MessageDigestToInterfacingScreenBridge implements BridgeInterface
{
    public function supports(object $payload, string $target): bool
    {
        return $payload instanceof MessageThreadDigestValue && BridgeTarget::MESSAGING_INTERFACING_DIGEST_SCREEN === $target;
    }

    /**
     * @return array<string, mixed>
     */
    public function bridge(object $payload, string $target, array $context = []): array
    {
        if (!$payload instanceof MessageThreadDigestValue || !$this->supports($payload, $target)) {
            throw new \InvalidArgumentException('Unsupported bridge payload for Messaging → Interfacing digest bridge.');
        }

        return [
            'id' => 'message.digest',
            'title' => $payload->title,
            'subtitle' => $payload->summary,
            'kind' => 'digest',
            'eyebrow' => 'Messaging · Digest',
            'items' => $payload->items,
            'itemCount' => count($payload->items),
            'bridgeContext' => $context,
        ];
    }
}
