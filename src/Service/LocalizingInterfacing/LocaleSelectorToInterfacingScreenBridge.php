<?php

declare(strict_types=1);

namespace App\Bridging\Service\LocalizingInterfacing;

use App\Bridging\Bridge\Contract\BridgeInterface;
use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use App\Bridging\ServiceInterface\LocalizingInterfacing\LocaleSelectorToInterfacingScreenBridgeInterface;
use App\Localizing\ValueObject\LocaleTemplateContext;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('bridging.dispatch_bridge')]
final readonly class LocaleSelectorToInterfacingScreenBridge implements LocaleSelectorToInterfacingScreenBridgeInterface
{
    public function __construct(
        private InterfacingScreenPayloadNormalizer $payloadNormalizer,
    ) {
    }

    public function supports(object $payload, string $target): bool
    {
        return $payload instanceof LocaleTemplateContext && BridgeTarget::SCREEN_LOCALIZING_LOCALE_SELECTOR === $target;
    }

    /**
     * @return array<string, mixed>
     */
    public function bridge(object $payload, string $target, array $context = []): mixed
    {
        if (!$payload instanceof LocaleTemplateContext || !$this->supports($payload, $target)) {
            throw new \InvalidArgumentException('Unsupported bridge payload for Localizing → Interfacing locale selector bridge.');
        }

        return $this->bridgeToScreen($payload, $context);
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function bridgeToScreen(LocaleTemplateContext $payload, array $context = []): array
    {
        $contextArray = $payload->toArray();

        $items = [];
        foreach ($contextArray['selector'] as $selector) {
            if (!\is_array($selector)) {
                continue;
            }

            $items[] = [
                'title' => sprintf('%s (%s)', (string) ($selector['name'] ?? ''), (string) ($selector['code'] ?? '')),
                'subtitle' => (string) ($selector['nativeName'] ?? ''),
                'meta' => [
                    'code' => (string) ($selector['code'] ?? ''),
                    'current' => !empty($selector['current']) ? 'yes' : 'no',
                    'default' => !empty($selector['default']) ? 'yes' : 'no',
                ],
            ];
        }

        return $this->payloadNormalizer->normalize([
            'id' => 'localizing.locale.selector',
            'kind' => 'collection',
            'title' => 'Locale selector',
            'subtitle' => 'Inspect the active locale, fallback chain, and available locale variants.',
            'eyebrow' => 'Localizing · Interfacing',
            'items' => $items,
            'facts' => [
                ['label' => 'Current locale', 'value' => $contextArray['currentLocale']],
                ['label' => 'Default locale', 'value' => $contextArray['defaultLocale']],
                ['label' => 'Fallback chain', 'value' => implode(' → ', $contextArray['fallbackChain'])],
            ],
            'meta' => [
                'domains' => $contextArray['domains'],
                'messages' => $contextArray['messages'],
            ],
        ], $context);
    }
}
