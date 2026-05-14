<?php

declare(strict_types=1);

namespace App\Bridging\Bridge\Contract;

/**
 * Canonical visible-route adoption contract for Interfacing-owned provider UI.
 *
 * Bridge owns route/resource/component adoption. Interfacing owns the shell,
 * provider mount, schema, and rendering through Ant Design ProComponents first
 * and PrimeReact second. Consumer repositories must not be patched as the
 * primary route to get a modern admin/workbench surface.
 */
final class BridgeVisibleProviderSurfaceRouteContract
{
    public const TEMPLATE = 'interfacing/bridge/provider_surface.html.twig';

    public const CONTROLLER = 'App\\Bridging\\Controller\\Interfacing\\BridgeVisibleProviderSurfaceController::show';

    public const PRIMARY_PROVIDER = 'antd-pro';
    public const SECONDARY_PROVIDER = 'primereact';

    /** @var list<string> */
    public const CANONICAL_VISIBLE_PREFIXES = [
        'catalog',
        'crud',
        'cruding',
        'vendor',
        'vendoring',
    ];

    /** @var array<string, string> */
    public const PREFIX_COMPONENT_MAP = [
        'catalog' => 'cataloging',
        'crud' => 'cruding',
        'cruding' => 'cruding',
        'vendor' => 'vendoring',
        'vendoring' => 'vendoring',
    ];

    /** @var list<string> */
    public const FORBIDDEN_DIRECT_OUTPUT_MARKERS = [
        '<table',
        '<form',
        '<style',
        'btn btn-',
        'container-fluid',
        'class="row"',
        "class='row'",
    ];

    private function __construct() {}
}
