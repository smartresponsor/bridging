<?php

declare(strict_types=1);

namespace App\Bridging\Bridge\Contract;

final class BridgeTarget
{
    public const WORKBENCH_CRUD = 'interfacing.crud.workbench';
    public const SCREEN_ACCESSING = 'interfacing.screen.accessing';
    public const SCREEN_MESSAGE = 'interfacing.screen.message';
    public const SCREEN_MESSAGE_DIGEST = 'interfacing.screen.message.digest';
    public const SCREEN_MESSAGE_NOTIFICATION_INBOX = 'interfacing.screen.message.notification.inbox';
    public const SCREEN_MESSAGE_SEARCH_RESULTS = 'interfacing.screen.message.search.results';
    public const SCREEN_MESSAGE_ROOM_COLLECTION = 'interfacing.screen.message.room.collection';
    public const SCREEN_LOCALIZING_LOCALE_SELECTOR = 'interfacing.screen.localizing.locale.selector';
    public const SCREEN_PAGING_PAGE = 'interfacing.screen.page';
    public const SCREEN_CURRENCING_TEMPLATE_CONTEXT = 'interfacing.screen.currencing.template.context';
    public const SCREEN_SUBSCRIPTING_PRESENTATION = 'interfacing.screen.subscripting.presentation';
    public const APP_HOST_DASHBOARD_PROVIDER_SURFACE = 'interfacing.app_host.dashboard.provider_surface';

    public const CRUDING_INTERFACING_WORKBENCH = self::WORKBENCH_CRUD;
    public const ACCESSING_INTERFACING_SCREEN = self::SCREEN_ACCESSING;
    public const MESSAGING_INTERFACING_SCREEN = self::SCREEN_MESSAGE;
    public const MESSAGING_INTERFACING_DIGEST_SCREEN = self::SCREEN_MESSAGE_DIGEST;
    public const MESSAGING_INTERFACING_NOTIFICATION_INBOX_SCREEN = self::SCREEN_MESSAGE_NOTIFICATION_INBOX;
    public const MESSAGING_INTERFACING_SEARCH_RESULTS_SCREEN = self::SCREEN_MESSAGE_SEARCH_RESULTS;
    public const MESSAGING_INTERFACING_ROOM_COLLECTION_SCREEN = self::SCREEN_MESSAGE_ROOM_COLLECTION;
    public const LOCALIZING_INTERFACING_SCREEN = self::SCREEN_LOCALIZING_LOCALE_SELECTOR;
    public const PAGING_INTERFACING_SCREEN = self::SCREEN_PAGING_PAGE;
    public const CURRENCING_INTERFACING_TEMPLATE_CONTEXT_SCREEN = self::SCREEN_CURRENCING_TEMPLATE_CONTEXT;
    public const CURRENCING_INTERFACING_SCREEN = self::SCREEN_CURRENCING_TEMPLATE_CONTEXT;
    public const SUBSCRIPTING_INTERFACING_PRESENTATION_SCREEN = self::SCREEN_SUBSCRIPTING_PRESENTATION;
    public const SUBSCRIPTING_INTERFACING_SCREEN = self::SCREEN_SUBSCRIPTING_PRESENTATION;
    public const APP_HOST_INTERFACING_DASHBOARD_PROVIDER_SURFACE = self::APP_HOST_DASHBOARD_PROVIDER_SURFACE;

    private function __construct() {}
}
