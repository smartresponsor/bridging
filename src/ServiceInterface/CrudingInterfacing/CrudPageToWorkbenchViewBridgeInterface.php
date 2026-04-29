<?php

declare(strict_types=1);

namespace App\Bridging\ServiceInterface\CrudingInterfacing;

use App\Bridging\Bridge\Contract\BridgeInterface;
use App\Interfacing\Contract\Crud\CrudWorkbenchView;

interface CrudPageToWorkbenchViewBridgeInterface extends BridgeInterface
{
    /**
     * Expected primary payload: App\Cruding\Dto\Crud\CrudPageDefinition.
     *
     * @param array<string, mixed> $context
     */
    public function bridgeToWorkbench(object $payload, array $context = []): CrudWorkbenchView;
}
