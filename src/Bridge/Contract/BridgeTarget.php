<?php

declare(strict_types=1);

namespace App\Bridging\Bridge\Contract;

final class BridgeTarget
{
    public const CRUDING_INTERFACING_WORKBENCH = 'interfacing.crud.workbench';
    public const ACCESSING_INTERFACING_SCREEN = 'interfacing.screen.accessing';
    public const MESSAGING_INTERFACING_SCREEN = 'interfacing.screen.message';
    public const MESSAGING_INTERFACING_DIGEST_SCREEN = 'interfacing.screen.messaging.digest';
    public const MESSAGING_INTERFACING_NOTIFICATION_INBOX_SCREEN = 'interfacing.screen.messaging.notification.inbox';
    public const MESSAGING_INTERFACING_SEARCH_RESULTS_SCREEN = 'interfacing.screen.messaging.search.results';
    public const MESSAGING_INTERFACING_ROOM_COLLECTION_SCREEN = 'interfacing.screen.messaging.room.collection';

    private function __construct()
    {
    }
}
