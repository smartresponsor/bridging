<?php

declare(strict_types=1);

namespace App\Bridging\Service\SubscriptingInterfacing;

use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\Bridge\Support\InterfacingScreenPayloadNormalizer;
use App\Bridging\ServiceInterface\SubscriptingInterfacing\SubscriptionPresentationToInterfacingScreenBridgeInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('bridging.dispatch_bridge')]
final readonly class SubscriptionPresentationToInterfacingScreenBridge implements SubscriptionPresentationToInterfacingScreenBridgeInterface
{
    public function __construct(
        private InterfacingScreenPayloadNormalizer $payloadNormalizer,
    ) {}

    public function supports(object $payload, string $target): bool
    {
        return BridgeTarget::SCREEN_SUBSCRIPTING_PRESENTATION === $target
            && $this->isSubscriptingPresentationPayload($payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function bridge(object $payload, string $target, array $context = []): array
    {
        if (!$this->supports($payload, $target)) {
            throw new \InvalidArgumentException('Unsupported bridge payload for Subscripting → Interfacing presentation bridge.');
        }

        return $this->bridgeToScreen($payload, $context);
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function bridgeToScreen(object $payload, array $context = []): array
    {
        $presentation = $this->presentationToArray($payload);
        $state = $this->arrayValue($presentation, ['state']);
        $planOptions = $this->listValue($presentation, ['planOptions', 'plan_options']);
        $entitlements = $this->listValue($presentation, ['entitlements']);
        $actions = $this->listValue($presentation, ['actions']);
        $commerce = $this->arrayValue($presentation, ['commerce']);
        $slots = $this->arrayValue($presentation, ['slots']);
        $metadata = $this->arrayValue($presentation, ['metadata', 'meta']);

        $subjectType = $this->stringValue($presentation, ['subjectType', 'subject_type'], $this->stringValue($state, ['subjectType', 'subject_type'], 'subject'));
        $subjectId = $this->stringValue($presentation, ['subjectId', 'subject_id'], $this->stringValue($state, ['subjectId', 'subject_id'], 'unknown'));
        $status = $this->stringValue($state, ['status'], 'none');
        $planCode = $this->stringValue($state, ['planCode', 'plan_code'], '');
        $planName = $this->stringValue($state, ['planName', 'plan_name'], $planCode);
        $contract = $this->stringValue($presentation, ['contract'], 'subscription.presentation.v1');

        return $this->payloadNormalizer->normalize([
            'id' => sprintf('subscripting.%s.%s.presentation', $subjectType, $subjectId),
            'kind' => 'dashboard',
            'title' => 'Subscription',
            'subtitle' => $this->subtitle($status, $planName),
            'eyebrow' => 'Subscripting · Interfacing',
            'primaryActions' => $this->mapActions($actions),
            'items' => [
                [
                    'title' => 'Current subscription',
                    'subtitle' => $this->subtitle($status, $planName),
                    'meta' => [
                        'type' => 'subscription.state',
                        'status' => $status,
                        'planCode' => $planCode,
                        'subjectType' => $subjectType,
                        'subjectId' => $subjectId,
                        'state' => $state,
                    ],
                ],
                [
                    'title' => 'Available plans',
                    'subtitle' => sprintf('%d plan option(s)', count($planOptions)),
                    'meta' => [
                        'type' => 'subscription.plan_options',
                        'plans' => $this->mapPlanOptions($planOptions),
                    ],
                ],
                [
                    'title' => 'Entitlements',
                    'subtitle' => sprintf('%d entitlement(s)', count($entitlements)),
                    'meta' => [
                        'type' => 'subscription.entitlements',
                        'entitlements' => $this->mapEntitlements($entitlements),
                    ],
                ],
            ],
            'facts' => [
                ['label' => 'Subject', 'value' => sprintf('%s:%s', $subjectType, $subjectId)],
                ['label' => 'Status', 'value' => $status],
                ['label' => 'Plan', 'value' => '' !== $planName ? $planName : 'None'],
                ['label' => 'Entitlements', 'value' => (string) count($entitlements)],
            ],
            'meta' => [
                'source' => 'subscripting.presentation',
                'contract' => $contract,
                'component' => $this->stringValue($presentation, ['component'], 'subscripting'),
                'state' => $state,
                'planOptions' => $this->mapPlanOptions($planOptions),
                'entitlements' => $this->mapEntitlements($entitlements),
                'commerce' => $commerce,
                'slots' => $slots,
                'metadata' => $metadata,
                'presentationPayload' => $presentation,
            ],
        ], $context);
    }

    private function isSubscriptingPresentationPayload(object $payload): bool
    {
        $className = $payload::class;
        if (str_ends_with($className, '\\SubscriptionPresentationPayloadDto')) {
            return true;
        }

        $presentation = $this->presentationToArray($payload);
        if ([] === $presentation) {
            return false;
        }

        $component = strtolower($this->stringValue($presentation, ['component'], ''));
        $contract = strtolower($this->stringValue($presentation, ['contract'], ''));

        return 'subscripting' === $component
            && str_starts_with($contract, 'subscription.presentation');
    }

    /**
     * @return array<string, mixed>
     */
    private function presentationToArray(object $payload): array
    {
        if (method_exists($payload, 'toArray')) {
            $value = $payload->toArray();

            return is_array($value) ? $this->normalizeArray($value) : [];
        }

        return $this->normalizeArray(get_object_vars($payload));
    }

    /**
     * @param array<mixed> $value
     *
     * @return array<string, mixed>
     */
    private function normalizeArray(array $value): array
    {
        $result = [];
        foreach ($value as $key => $item) {
            $normalizedKey = is_int($key) ? $key : (string) $key;
            if (is_array($item)) {
                $result[$normalizedKey] = $this->normalizeArray($item);
                continue;
            }

            if ($item instanceof \DateTimeInterface) {
                $result[$normalizedKey] = $item->format(DATE_ATOM);
                continue;
            }

            if ($item instanceof \BackedEnum) {
                $result[$normalizedKey] = $item->value;
                continue;
            }

            if (is_object($item)) {
                $result[$normalizedKey] = $this->presentationToArray($item);
                continue;
            }

            $result[$normalizedKey] = $item;
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $source
     * @param list<string> $keys
     */
    private function stringValue(array $source, array $keys, string $default): string
    {
        foreach ($keys as $key) {
            $value = $source[$key] ?? null;
            if (is_string($value) && '' !== trim($value)) {
                return $value;
            }

            if (is_int($value) || is_float($value)) {
                return (string) $value;
            }
        }

        return $default;
    }

    /**
     * @param array<string, mixed> $source
     * @param list<string> $keys
     *
     * @return array<string, mixed>
     */
    private function arrayValue(array $source, array $keys): array
    {
        foreach ($keys as $key) {
            $value = $source[$key] ?? null;
            if (is_array($value)) {
                return $value;
            }
        }

        return [];
    }

    /**
     * @param array<string, mixed> $source
     * @param list<string> $keys
     *
     * @return list<array<string, mixed>>
     */
    private function listValue(array $source, array $keys): array
    {
        foreach ($keys as $key) {
            $value = $source[$key] ?? null;
            if (!is_array($value)) {
                continue;
            }

            $result = [];
            foreach ($value as $item) {
                if (is_array($item)) {
                    $result[] = $item;
                }
            }

            return $result;
        }

        return [];
    }

    /**
     * @param list<array<string, mixed>> $actions
     *
     * @return list<array<string, mixed>>
     */
    private function mapActions(array $actions): array
    {
        $result = [];
        foreach ($actions as $action) {
            $code = $this->stringValue($action, ['code'], '');
            $label = $this->stringValue($action, ['label'], $code);
            if ('' === $code && '' === $label) {
                continue;
            }

            $result[] = [
                'label' => $label,
                'intent' => $this->stringValue($action, ['intent'], $code),
                'enabled' => (bool) ($action['enabled'] ?? true),
                'payload' => is_array($action['payload'] ?? null) ? $action['payload'] : [],
                'meta' => [
                    'code' => $code,
                    'source' => 'subscripting.presentation.action',
                ],
            ];
        }

        return $result;
    }

    /**
     * @param list<array<string, mixed>> $planOptions
     *
     * @return list<array<string, mixed>>
     */
    private function mapPlanOptions(array $planOptions): array
    {
        $result = [];
        foreach ($planOptions as $plan) {
            $code = $this->stringValue($plan, ['code'], '');
            if ('' === $code) {
                continue;
            }

            $result[] = [
                'code' => $code,
                'name' => $this->stringValue($plan, ['name'], $code),
                'periodCount' => (int) ($plan['periodCount'] ?? $plan['period_count'] ?? 0),
                'periodUnit' => $this->stringValue($plan, ['periodUnit', 'period_unit'], ''),
                'trialDays' => (int) ($plan['trialDays'] ?? $plan['trial_days'] ?? 0),
                'currencyCode' => $this->stringValue($plan, ['currencyCode', 'currency_code'], ''),
                'amountMinorSnapshot' => $plan['amountMinorSnapshot'] ?? $plan['amount_minor_snapshot'] ?? null,
                'pricingReference' => $plan['pricingReference'] ?? $plan['pricing_reference'] ?? null,
                'attributes' => is_array($plan['attributes'] ?? null) ? $plan['attributes'] : [],
            ];
        }

        return $result;
    }

    /**
     * @param list<array<string, mixed>> $entitlements
     *
     * @return list<array<string, mixed>>
     */
    private function mapEntitlements(array $entitlements): array
    {
        $result = [];
        foreach ($entitlements as $entitlement) {
            $code = $this->stringValue($entitlement, ['code'], '');
            if ('' === $code) {
                continue;
            }

            $result[] = [
                'code' => $code,
                'granted' => (bool) ($entitlement['granted'] ?? false),
                'quantity' => $entitlement['quantity'] ?? null,
                'validFrom' => $entitlement['validFrom'] ?? $entitlement['valid_from'] ?? null,
                'validUntil' => $entitlement['validUntil'] ?? $entitlement['valid_until'] ?? null,
                'attributes' => is_array($entitlement['attributes'] ?? null) ? $entitlement['attributes'] : [],
            ];
        }

        return $result;
    }

    private function subtitle(string $status, string $planName): string
    {
        if ('none' === $status) {
            return 'No active subscription';
        }

        if ('' === $planName) {
            return sprintf('Status: %s', $status);
        }

        return sprintf('%s · %s', $planName, $status);
    }
}
