<?php

declare(strict_types=1);

namespace App\Bridging\ServiceInterface\AppHostInterfacing;

use Symfony\Component\HttpFoundation\Response;

interface AppDashboardSurfaceResponderInterface
{
    /**
     * @param array<string, mixed> $context
     */
    public function respond(object $surface, array $context = []): Response;
}
