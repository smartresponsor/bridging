<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$errors = [];

$requiredFiles = [
    'src/Bridge/Contract/BridgeVisibleProviderSurfaceRouteContract.php',
    'src/Controller/Interfacing/BridgeVisibleProviderSurfaceController.php',
    'config/component/routes.yaml',
    'root/VISIBLE_PROVIDER_ROUTE_ADOPTION.md',
];

foreach ($requiredFiles as $relativePath) {
    if (!is_file($root . '/' . $relativePath)) {
        $errors[] = 'Missing required visible provider route adoption file: ' . $relativePath;
    }
}

$routes = readProjectFile($root, 'config/component/routes.yaml');
foreach (['/catalog/', '/catalog/{resourcePath}', '/crud/', '/crud/{resourcePath}', '/cruding/', '/cruding/{resourcePath}', '/vendor/', '/vendor/{resourcePath}', '/vendoring/', '/vendoring/{resourcePath}'] as $route) {
    if (!str_contains($routes, 'path: ' . $route)) {
        $errors[] = 'Missing visible provider route: ' . $route;
    }
}

$controller = readProjectFile($root, 'src/Controller/Interfacing/BridgeVisibleProviderSurfaceController.php');
foreach ([
    'InterfacingRendererInterface',
    'BridgeVisibleProviderSurfaceRouteContract::TEMPLATE',
    "'bridgeComponent'",
    "'bridgeResource'",
    "'bridgeOperation'",
    "'bridgeSurface'",
] as $needle) {
    if (!str_contains($controller, $needle)) {
        $errors[] = 'Visible provider controller is missing canonical marker: ' . $needle;
    }
}

$combined = $controller . "\n" . readProjectFile($root, 'root/VISIBLE_PROVIDER_ROUTE_ADOPTION.md');
foreach (['<table', '<form', '<style', 'btn btn-', 'container-fluid', 'class="row"', "class='row'", 'EasyAdmin', 'Bootstrap'] as $forbidden) {
    if (str_contains($controller, $forbidden)) {
        $errors[] = 'Visible provider controller must not contain forbidden UI marker: ' . $forbidden;
    }
}

foreach (['Ant Design ProComponents is the primary provider', 'PrimeReact is the secondary', 'No fallback or legacy UI'] as $needle) {
    if (!str_contains($combined, $needle)) {
        $errors[] = 'Visible provider adoption docs/contract missing canon marker: ' . $needle;
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Bridging visible provider route adoption guard: FAILED\n");
    foreach ($errors as $error) {
        fwrite(STDERR, '- ' . $error . "\n");
    }
    exit(2);
}

fwrite(STDOUT, "Bridging visible provider route adoption guard: OK\n");

function readProjectFile(string $root, string $relativePath): string
{
    $path = $root . '/' . $relativePath;
    if (!is_file($path)) {
        return '';
    }

    return (string) file_get_contents($path);
}
