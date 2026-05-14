<?php

declare(strict_types=1);

namespace App\Bridging\Service\AppHostInterfacing;

use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Bridging\ServiceInterface\AppHostInterfacing\AppDashboardSurfaceToProviderSurfaceBridgeInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Normalizes App host dashboard composition into the Interfacing provider
 * surface payload used by the shared Ant Design ProComponents renderer.
 *
 * App remains the host route/composition owner. Bridging owns the handoff
 * boundary. Interfacing remains the rendering/provider owner.
 */
#[AutoconfigureTag('bridging.dispatch_bridge')]
final class AppDashboardSurfaceToProviderSurfaceBridge implements AppDashboardSurfaceToProviderSurfaceBridgeInterface
{
    private const SUPPORTED_PAYLOAD = 'App\\Dto\\Dashboard\\AppDashboardSurfacePayload';

    public function supports(object $payload, string $target): bool
    {
        return self::SUPPORTED_PAYLOAD === $payload::class
            && BridgeTarget::APP_HOST_DASHBOARD_PROVIDER_SURFACE === $target;
    }

    public function bridge(object $payload, string $target, array $context = []): mixed
    {
        if (!$this->supports($payload, $target)) {
            throw new \InvalidArgumentException(sprintf(
                'Unsupported App dashboard bridge request for payload "%s" and target "%s".',
                $payload::class,
                $target,
            ));
        }

        return $this->bridgeToProviderSurface($payload, $context);
    }

    public function bridgeToProviderSurface(object $payload, array $context = []): array
    {
        $surface = $this->payloadToArray($payload);
        $routeContext = $this->normalizeRouteContext($this->arrayValue($surface['routeContext'] ?? []));

        $surface['title'] = $this->stringValue($surface['title'] ?? null, 'Commerce Control Center');
        $surface['component'] = 'app-host';
        $surface['integrationOwner'] = 'bridging-app-host-interfacing';
        $surface['bridgeOwner'] = 'bridging';
        $surface['bridgeTarget'] = BridgeTarget::APP_HOST_DASHBOARD_PROVIDER_SURFACE;
        $surface['renderingOwner'] = 'interfacing';
        $surface['primaryProvider'] = 'ant-design-procomponents';
        $surface['secondaryProvider'] = 'primereact';
        $surface['shellMode'] = 'provider-page';
        $surface['routeContext'] = $routeContext;

        $surface['columns'] = $this->listValue($surface['columns'] ?? []);
        $surface['rows'] = $this->listValue($surface['rows'] ?? []);
        $surface['filters'] = $this->listValue($surface['filters'] ?? []);
        $surface['formFields'] = $this->listValue($surface['formFields'] ?? []);
        $surface['formSections'] = $this->listValue($surface['formSections'] ?? []);
        $surface['headerActions'] = $this->listValue($surface['headerActions'] ?? []);
        $surface['paginationLabel'] = $this->stringValue(
            $surface['paginationLabel'] ?? null,
            'App-owned dashboard sections exposed through Bridging and rendered by Interfacing.',
        );

        $surface['bridgeContext'] = [
            'source' => 'app-host-dashboard',
            'target' => BridgeTarget::APP_HOST_DASHBOARD_PROVIDER_SURFACE,
            'runtimeOwner' => 'app-host',
            'compositionOwner' => 'app-host',
            'handoffOwner' => 'bridging',
            'renderingOwner' => 'interfacing',
            'requestUri' => $this->stringValue($context['request_uri'] ?? null, '/'),
        ];

        return $surface;
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadToArray(object $payload): array
    {
        if (!method_exists($payload, 'toArray')) {
            throw new \InvalidArgumentException(sprintf(
                'App dashboard bridge payload "%s" must expose toArray().',
                $payload::class,
            ));
        }

        $data = $payload->toArray();
        if (!is_array($data)) {
            throw new \InvalidArgumentException(sprintf(
                'App dashboard bridge payload "%s" returned a non-array surface.',
                $payload::class,
            ));
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    private function normalizeRouteContext(array $context): array
    {
        return [
            'resourcePath' => $this->stringValue($context['resourcePath'] ?? null, 'app-dashboard'),
            'resourceLabel' => $this->stringValue($context['resourceLabel'] ?? null, 'Commerce Control Center'),
            'resourceCollectionLabel' => $this->stringValue($context['resourceCollectionLabel'] ?? null, 'Application dashboard'),
            'operation' => $this->stringValue($context['operation'] ?? null, 'overview'),
            'surface' => $this->stringValue($context['surface'] ?? null, 'dashboard'),
            'mode' => $this->stringValue($context['mode'] ?? null, 'collection'),
            'collectionHref' => $this->stringValue($context['collectionHref'] ?? null, '/'),
        ];
    }

    /**
     * @param mixed $value
     *
     * @return array<string, mixed>
     */
    private function arrayValue(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    /**
     * @param mixed $value
     *
     * @return list<mixed>
     */
    private function listValue(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values($value);
    }

    private function stringValue(mixed $value, string $fallback): string
    {
        if (is_scalar($value) && '' !== trim((string) $value)) {
            return (string) $value;
        }

        return $fallback;
    }
}
