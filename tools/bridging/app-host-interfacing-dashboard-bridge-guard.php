<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$required = [
    'src/Service/AppHostInterfacing/AppDashboardSurfaceToProviderSurfaceBridge.php',
    'src/ServiceInterface/AppHostInterfacing/AppDashboardSurfaceToProviderSurfaceBridgeInterface.php',
    'config/component/services_app_host_interfacing.yaml',
    'src/Bridge/Contract/BridgeTarget.php',
    'root/APP_HOST_INTERFACING_BRIDGE.md',
];

foreach ($required as $file) {
    if (!is_file($root . '/' . str_replace('/', DIRECTORY_SEPARATOR, $file))) {
        fwrite(STDERR, "Missing App host Interfacing bridge file: {$file}\n");
        exit(1);
    }
}

$target = file_get_contents($root . '/src/Bridge/Contract/BridgeTarget.php');
if (!str_contains($target, 'APP_HOST_DASHBOARD_PROVIDER_SURFACE')) {
    fwrite(STDERR, "BridgeTarget is missing APP_HOST_DASHBOARD_PROVIDER_SURFACE.\n");
    exit(1);
}

$service = file_get_contents($root . '/src/Service/AppHostInterfacing/AppDashboardSurfaceToProviderSurfaceBridge.php');
foreach ([
    'bridging-app-host-interfacing',
    'BridgeTarget::APP_HOST_DASHBOARD_PROVIDER_SURFACE',
    "#[AutoconfigureTag('bridging.dispatch_bridge')]",
    'App\\\\Dto\\\\Dashboard\\\\AppDashboardSurfacePayload',
] as $needle) {
    if (!str_contains($service, $needle)) {
        fwrite(STDERR, "App dashboard bridge is missing marker: {$needle}\n");
        exit(1);
    }
}

$config = file_get_contents($root . '/config/component/services_app_host_interfacing.yaml');
if (!str_contains($config, 'App\\Bridging\\ServiceInterface\\AppHostInterfacing\\AppDashboardSurfaceToProviderSurfaceBridgeInterface')) {
    fwrite(STDERR, "App host Interfacing bridge service alias is missing.\n");
    exit(1);
}

fwrite(STDOUT, "App host Interfacing dashboard bridge guard passed.\n");
