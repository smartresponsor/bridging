<?php

declare(strict_types=1);

namespace App\Bridging\Service\AppHostInterfacing;

use App\Bridging\ServiceInterface\AppHostInterfacing\AppDashboardSurfaceResponderInterface;
use App\Bridging\ServiceInterface\AppHostInterfacing\AppDashboardSurfaceToProviderSurfaceBridgeInterface;
use App\Interfacing\ServiceInterface\Interfacing\Presentation\InterfacingRendererInterface;
use Symfony\Component\HttpFoundation\Response;

final readonly class AppDashboardSurfaceResponder implements AppDashboardSurfaceResponderInterface
{
    private const TEMPLATE = 'bridging/app-host/dashboard.html.twig';

    public function __construct(
        private InterfacingRendererInterface $renderer,
        private AppDashboardSurfaceToProviderSurfaceBridgeInterface $surfaceBridge,
    ) {}

    public function respond(object $surface, array $context = []): Response
    {
        $workbench = $this->surfaceBridge->bridgeToProviderSurface($surface, $context);
        $routeContext = $workbench['routeContext'] ?? [];

        return $this->renderer->render(self::TEMPLATE, [
            'workbench' => $workbench,
            'shell' => [
                'activeId' => 'applications.dashboard',
                'activeSection' => 'workspace',
                'rightPanelEnabled' => false,
            ],
            'title' => $workbench['title'] ?? 'Commerce Control Center',
            'adminProviderPageTitle' => $workbench['title'] ?? 'Commerce Control Center',
            'adminProviderResourceName' => $routeContext['resourcePath'] ?? 'app-dashboard',
            'adminProviderResourceLabel' => $routeContext['resourceLabel'] ?? 'Commerce Control Center',
            'adminProviderOperation' => $routeContext['operation'] ?? 'overview',
            'adminProviderSurface' => $routeContext['surface'] ?? 'dashboard',
            'adminProviderDefaultView' => 'table',
            'adminProviderViewModes' => ['table', 'cards'],
        ]);
    }
}
