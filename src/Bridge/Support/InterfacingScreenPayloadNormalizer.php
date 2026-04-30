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
        $normalized = $this->canonicalize($payload);
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

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function canonicalize(array $payload): array
    {
        return [
            'id' => $payload['id'] ?? null,
            'kind' => $payload['kind'] ?? null,
            'title' => $this->stringOrNull($payload['title'] ?? null),
            'subtitle' => $this->stringOrNull($payload['subtitle'] ?? null),
            'eyebrow' => $this->stringOrNull($payload['eyebrow'] ?? null),
            'formVariable' => $this->stringOrNull($payload['formVariable'] ?? null),
            'primaryActions' => $this->listOfArrays($payload['primaryActions'] ?? []),
            'secondaryActions' => $this->listOfArrays($payload['secondaryActions'] ?? []),
            'context' => is_array($payload['context'] ?? null) ? $payload['context'] : [],
            'items' => is_array($payload['items'] ?? null) ? $payload['items'] : [],
            'itemCount' => is_int($payload['itemCount'] ?? null) ? $payload['itemCount'] : (is_array($payload['items'] ?? null) ? count($payload['items']) : 0),
            'facts' => is_array($payload['facts'] ?? null) ? $payload['facts'] : [],
            'meta' => is_array($payload['meta'] ?? null) ? $payload['meta'] : [],
        ] + $payload;
    }

    private function stringOrNull(mixed $value): ?string
    {
        return \is_string($value) ? $value : null;
    }

    /**
     * @param mixed $value
     *
     * @return list<array<string, mixed>>
     */
    private function listOfArrays(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $result = [];
        foreach ($value as $item) {
            if (is_array($item)) {
                $result[] = $item;
            }
        }

        return $result;
    }
}
