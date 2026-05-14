<?php

declare(strict_types=1);

namespace App\Bridging\Service\ExchangingInterfacing;

use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use App\Bridging\ServiceInterface\ExchangingInterfacing\ExchangeTemplateContextToInterfacingScreenBridgeInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('bridging.dispatch_bridge')]
final readonly class ExchangeTemplateContextToInterfacingScreenBridge implements ExchangeTemplateContextToInterfacingScreenBridgeInterface
{
    public function __construct(
        private InterfacingScreenPayloadNormalizer $payloadNormalizer,
    ) {}

    public function supports(object $payload, string $target): bool
    {
        return BridgeTarget::SCREEN_EXCHANGING_TEMPLATE_CONTEXT === $target
            && $this->isExchangingTemplateContext($payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function bridge(object $payload, string $target, array $context = []): array
    {
        if (!$this->supports($payload, $target)) {
            throw new \InvalidArgumentException('Unsupported bridge payload for Exchanging → Interfacing template context bridge.');
        }

        return $this->bridgeToScreen($payload, $context);
    }

    /**
     * Converts an Exchanging outbound template-context object into an Interfacing screen payload.
     *
     * The bridge intentionally uses object/shape typing instead of importing Exchanging classes so the Bridging
     * package can be tested and booted before the Exchanging package is installed in the same host app.
     *
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function bridgeToScreen(object $payload, array $context = []): array
    {
        $templateContext = $this->templateContextToArray($payload);
        $money = $this->arrayValue($templateContext, ['money']);
        $rate = $this->arrayValue($templateContext, ['rate']);
        $provider = $this->arrayValue($templateContext, ['provider']);
        $freshness = $this->arrayValue($templateContext, ['freshness']);
        $audit = $this->arrayValue($templateContext, ['audit']);
        $links = $this->arrayValue($templateContext, ['links']);

        $baseCurrencyCode = $this->stringValue($money, ['baseCurrencyCode', 'baseCurrency', 'fromCurrencyCode'], 'USD');
        $quoteCurrencyCode = $this->stringValue($money, ['quoteCurrencyCode', 'quoteCurrency', 'toCurrencyCode'], 'UAH');
        $baseAmount = $this->stringValue($money, ['baseAmount', 'amount', 'sourceAmount'], '0');
        $quoteAmount = $this->stringValue($money, ['quoteAmount', 'convertedAmount', 'targetAmount'], '0');
        $rateValue = $this->stringValue($rate, ['rateValue', 'rate', 'value'], '');
        $rateDate = $this->stringValue($rate, ['rateDate', 'date'], '');
        $providerCode = $this->stringValue($provider, ['providerCode', 'provider', 'code'], '');
        $freshnessStatus = $this->stringValue($freshness, ['status', 'freshnessStatus'], 'unknown');
        $viewType = $this->stringValue($templateContext, ['viewType', 'templateSelector'], 'exchange_quote_summary');
        $title = $this->stringValue($templateContext, ['title'], sprintf('%s/%s exchange quote', $baseCurrencyCode, $quoteCurrencyCode));

        return $this->payloadNormalizer->normalize([
            'id' => 'exchanging.exchange.quote.summary',
            'kind' => 'summary',
            'title' => $title,
            'subtitle' => sprintf('%s %s → %s %s', $baseAmount, $baseCurrencyCode, $quoteAmount, $quoteCurrencyCode),
            'eyebrow' => 'Exchanging · Interfacing',
            'items' => $this->resolveItems($money, $rate, $provider, $freshness, $audit),
            'facts' => [
                ['label' => 'Base amount', 'value' => sprintf('%s %s', $baseAmount, $baseCurrencyCode)],
                ['label' => 'Quote amount', 'value' => sprintf('%s %s', $quoteAmount, $quoteCurrencyCode)],
                ['label' => 'Rate', 'value' => $rateValue],
                ['label' => 'Rate date', 'value' => $rateDate],
                ['label' => 'Provider', 'value' => $providerCode],
                ['label' => 'Freshness', 'value' => $freshnessStatus],
            ],
            'primaryActions' => $this->resolveActions($links),
            'meta' => [
                'source' => 'exchanging.template_context',
                'viewType' => $viewType,
                'templateSelector' => $viewType,
                'component' => $this->stringValue($templateContext, ['component'], 'Exchanging'),
                'money' => $money,
                'rate' => $rate,
                'provider' => $provider,
                'freshness' => $freshness,
                'audit' => $audit,
                'links' => $links,
                'templateContext' => $templateContext,
            ],
        ], $context);
    }

    private function isExchangingTemplateContext(object $payload): bool
    {
        $className = $payload::class;
        if (str_ends_with($className, '\ExchangeTemplateContextDto') || str_ends_with($className, '\ExchangeTemplateContext')) {
            return true;
        }

        $templateContext = $this->templateContextToArray($payload);
        if ([] === $templateContext) {
            return false;
        }

        $component = strtolower($this->stringValue($templateContext, ['component', 'componentKey'], ''));
        if ('exchanging' === $component || 'exchange' === $component) {
            return true;
        }

        return isset($templateContext['money'], $templateContext['rate'])
            && (isset($templateContext['provider']) || isset($templateContext['freshness']) || isset($templateContext['audit']));
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
            'component' => ['component', 'getComponent', 'componentKey', 'getComponentKey'],
            'viewType' => ['viewType', 'getViewType', 'templateSelector', 'getTemplateSelector'],
            'title' => ['title', 'getTitle'],
            'money' => ['money', 'getMoney'],
            'rate' => ['rate', 'getRate'],
            'provider' => ['provider', 'getProvider'],
            'freshness' => ['freshness', 'getFreshness'],
            'audit' => ['audit', 'getAudit'],
            'links' => ['links', 'getLinks'],
            'metadata' => ['metadata', 'getMetadata'],
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

        if ([] === $result) {
            foreach (get_object_vars($payload) as $key => $value) {
                $result[$key] = $this->normalizeMixed($value);
            }
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $money
     * @param array<string, mixed> $rate
     * @param array<string, mixed> $provider
     * @param array<string, mixed> $freshness
     * @param array<string, mixed> $audit
     *
     * @return list<array<string, mixed>>
     */
    private function resolveItems(array $money, array $rate, array $provider, array $freshness, array $audit): array
    {
        return [
            ['title' => 'Money', 'subtitle' => 'Base and quote amounts', 'meta' => $money],
            ['title' => 'Rate', 'subtitle' => 'Applied exchange rate', 'meta' => $rate],
            ['title' => 'Provider', 'subtitle' => 'Rate provider attribution', 'meta' => $provider],
            ['title' => 'Freshness', 'subtitle' => 'Rate freshness status', 'meta' => $freshness],
            ['title' => 'Audit', 'subtitle' => 'Applied-rate audit context', 'meta' => $audit],
        ];
    }

    /**
     * @param array<string, mixed> $links
     *
     * @return list<array<string, mixed>>
     */
    private function resolveActions(array $links): array
    {
        $actions = [];
        foreach ($links as $name => $url) {
            if (!is_scalar($url)) {
                continue;
            }
            $actions[] = ['label' => ucfirst((string) $name), 'target' => (string) $url];
        }

        return $actions;
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
     * @return array<string, mixed>
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
