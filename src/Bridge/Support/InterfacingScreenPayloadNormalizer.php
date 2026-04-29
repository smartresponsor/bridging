<?php

declare(strict_types=1);

namespace App\Bridging\Bridge\Support;

final class InterfacingScreenPayloadNormalizer
{
    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function normalize(array $payload, array $context = []): array
    {
        $normalized = $payload;
        $normalized['bridgeContext'] = $context;

        if (!isset($normalized['id']) || !\is_string($normalized['id']) || '' === trim($normalized['id'])) {
            throw new \InvalidArgumentException('Interfacing screen payload must define a non-empty string "id".');
        }

        if (!isset($normalized['kind']) || !\is_string($normalized['kind']) || '' === trim($normalized['kind'])) {
            throw new \InvalidArgumentException(sprintf(
                'Interfacing screen payload "%s" must define a non-empty string "kind".',
                $normalized['id'],
            ));
        }

        return $normalized;
    }
}
