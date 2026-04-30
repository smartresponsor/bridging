<?php

declare(strict_types=1);

namespace App\Bridging\Service\MessagingInterfacing;

use App\Bridging\Bridge\Contract\BridgeInterface;
use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Value\Thread\MessageThreadDigestValue;
use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('bridging.dispatch_bridge')]
final readonly class MessageDigestToInterfacingScreenBridge implements BridgeInterface
{
    public function __construct(
        private InterfacingScreenPayloadNormalizer $payloadNormalizer,
    ) {
    }

    public function supports(object $payload, string $target): bool
    {
        return $payload instanceof MessageThreadDigestValue && BridgeTarget::SCREEN_MESSAGE_DIGEST === $target;
    }

    /**
     * @return array<string, mixed>
     */
    public function bridge(object $payload, string $target, array $context = []): array
    {
        if (!$payload instanceof MessageThreadDigestValue || !$this->supports($payload, $target)) {
            throw new \InvalidArgumentException('Unsupported bridge payload for Messaging → Interfacing digest bridge.');
        }

        return $this->payloadNormalizer->normalize([
            'id' => 'message.digest',
            'title' => $payload->title,
            'subtitle' => $payload->summary,
            'kind' => 'digest',
            'eyebrow' => 'Messaging · Digest',
            'items' => $payload->items,
            'itemCount' => count($payload->items),
        ], $context);
    }
}
