<?php

declare(strict_types=1);

namespace App\Bridging\Service\CurrencingInterfacing;

use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use App\Bridging\ServiceInterface\CurrencingInterfacing\CurrencyTemplateContextToInterfacingScreenBridgeInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('bridging.dispatch_bridge')]
final readonly class CurrencyTemplateContextToInterfacingScreenBridge implements CurrencyTemplateContextToInterfacingScreenBridgeInterface
{
    public function __construct(
        private InterfacingScreenPayloadNormalizer $payloadNormalizer,
    ) {
    }

    public function supports(object $payload, string $target): bool
    {
        return BridgeTarget::SCREEN_CURRENCING_TEMPLATE_CONTEXT === $target
            && $this->isCurrencingTemplateContext($payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function bridge(object $payload, string $target, array $context = []): array
    {
        if (!$this->supports($payload, $target)) {
            throw new \InvalidArgumentException('Unsupported bridge payload for Currencing → Interfacing template context bridge.');
        }

        return $this->bridgeToScreen($payload, $context);
    }

    /**
     * Converts a Currencing outbound template-context object into an Interfacing screen payload.
     *
     * The bridge intentionally uses object/shape typing instead of importing Currencing classes so the Bridging
     * package can be tested and booted before the Currencing package is installed in the same host app.
     *
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function bridgeToScreen(object $payload, array $context = []): array
    {
        $templateContext = $this->templateContextToArray($payload);
        $selectedCurrencyCode = $this->stringValue($templateContext, ['selectedCurrencyCode', 'selectedCode', 'currencyCode'], 'USD');
        $locale = $this->stringValue($templateContext, ['locale'], 'en');
        $componentKey = $this->stringValue($templateContext, ['componentKey'], 'currencing');

        return $this->payloadNormalizer->normalize([
            'id' => 'currencing.currency.selector',
            'kind' => 'collection',
            'title' => 'Currency selector',
            'subtitle' => sprintf('Selected currency: %s · Locale: %s', $selectedCurrencyCode, $locale),
            'eyebrow' => 'Currencing · Interfacing',
            'items' => $this->resolveItems($templateContext),
            'facts' => [
                ['label' => 'Component', 'value' => $componentKey],
                ['label' => 'Selected currency', 'value' => $selectedCurrencyCode],
                ['label' => 'Locale', 'value' => $locale],
            ],
            'meta' => [
                'routeNames' => $this->arrayValue($templateContext, ['routeNames', 'routes']),
                'capabilities' => $this->arrayValue($templateContext, ['capabilities']),
                'source' => 'currencing.template_context',
                'templateContext' => $templateContext,
            ],
        ], $context);
    }

    private function isCurrencingTemplateContext(object $payload): bool
    {
        $className = $payload::class;
        if (str_ends_with($className, '\\CurrencyTemplateContext')) {
            return true;
        }

        $templateContext = $this->templateContextToArray($payload);
        if ([] === $templateContext) {
            return false;
        }

        $componentKey = $this->stringValue($templateContext, ['componentKey'], '');
        if ('currencing' === strtolower($componentKey)) {
            return true;
        }

        return isset($templateContext['selector'])
            && (isset($templateContext['selectedCurrencyCode']) || isset($templateContext['currencyCode']));
    }

    /**
     * @return array<string, mixed>
     */
    private function templateContextToArray(object $payload): array
    {
        if (method_exists($payload, 'toArray')) {
            $array = $payload->toArray();

            return is_array($array) ? $array : [];
        }

        $readerMethods = [
            'componentKey' => ['componentKey', 'getComponentKey'],
            'selectedCurrencyCode' => ['selectedCurrencyCode', 'getSelectedCurrencyCode', 'selectedCode', 'getSelectedCode'],
            'locale' => ['locale', 'getLocale'],
            'selector' => ['selector', 'getSelector'],
            'metadata' => ['metadata', 'getMetadata', 'metadataList', 'getMetadataList', 'currencies', 'getCurrencies'],
            'routeNames' => ['routeNames', 'getRouteNames', 'routes', 'getRoutes'],
            'capabilities' => ['capabilities', 'getCapabilities'],
        ];

        $result = [];
        foreach ($readerMethods as $key => $methods) {
            foreach ($methods as $method) {
                if (method_exists($payload, $method)) {
                    $result[$key] = $this->normalizeMixed($payload->{$method}());
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $templateContext
     *
     * @return list<array<string, mixed>>
     */
    private function resolveItems(array $templateContext): array
    {
        $candidates = [
            $templateContext['selector']['options'] ?? null,
            $templateContext['selector']['items'] ?? null,
            $templateContext['selector'] ?? null,
            $templateContext['metadata'] ?? null,
            $templateContext['metadataList'] ?? null,
            $templateContext['currencies'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            $items = $this->normalizeItems($candidate);
            if ([] !== $items) {
                return $items;
            }
        }

        return [];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function normalizeItems(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $items = [];
        foreach ($value as $row) {
            $row = $this->normalizeMixed($row);
            if (!is_array($row)) {
                continue;
            }

            $code = $this->stringValue($row, ['code', 'currencyCode', 'value'], '');
            $label = $this->stringValue($row, ['label', 'displayName', 'name', 'title'], $code);
            $minorUnit = $row['minorUnit'] ?? $row['minorUnits'] ?? $row['precision'] ?? null;
            $selected = (bool) ($row['selected'] ?? $row['current'] ?? false);

            $items[] = [
                'title' => '' !== $label && '' !== $code ? sprintf('%s (%s)', $label, $code) : ($label ?: $code),
                'subtitle' => $this->stringValue($row, ['symbol', 'nativeSymbol', 'formatExample'], ''),
                'meta' => [
                    'code' => $code,
                    'minorUnit' => is_int($minorUnit) ? (string) $minorUnit : (is_numeric($minorUnit) ? (string) (int) $minorUnit : null),
                    'selected' => $selected ? 'yes' : 'no',
                ],
            ];
        }

        return $items;
    }

    /**
     * @return array<string, mixed>|list<mixed>|scalar|null
     */
    private function normalizeMixed(mixed $value): mixed
    {
        if (is_array($value) || null === $value || is_scalar($value)) {
            return $value;
        }

        if (is_object($value) && method_exists($value, 'toArray')) {
            $array = $value->toArray();

            return is_array($array) ? $array : [];
        }

        if (is_object($value)) {
            $result = [];
            foreach (get_object_vars($value) as $key => $item) {
                $result[$key] = $this->normalizeMixed($item);
            }

            return $result;
        }

        return null;
    }

    /**
     * @param array<string, mixed> $source
     * @param list<string>         $keys
     */
    private function stringValue(array $source, array $keys, string $default): string
    {
        foreach ($keys as $key) {
            if (isset($source[$key]) && is_scalar($source[$key])) {
                $value = trim((string) $source[$key]);
                if ('' !== $value) {
                    return $value;
                }
            }
        }

        return $default;
    }

    /**
     * @param array<string, mixed> $source
     * @param list<string>         $keys
     *
     * @return array<string, mixed>|list<mixed>
     */
    private function arrayValue(array $source, array $keys): array
    {
        foreach ($keys as $key) {
            if (isset($source[$key]) && is_array($source[$key])) {
                return $source[$key];
            }
        }

        return [];
    }
}
