<?php

declare(strict_types=1);

namespace App\Bridging\Service\AccessingInterfacing;

use App\Accessing\Dto\PageView;
use App\Accessing\ServiceInterface\Rendering\PageResponderInterface;
use App\Bridging\Bridge\Contract\BridgeTarget;
use App\Interfacing\ServiceInterface\Interfacing\Presentation\InterfacingRendererInterface;
use Symfony\Component\HttpFoundation\Response;

final readonly class AccessingInterfacingPageResponder implements PageResponderInterface
{
    private const TEMPLATE = 'bridging/accessing/screen.html.twig';

    public function __construct(
        private InterfacingRendererInterface $renderer,
        private AccessingPageToInterfacingScreenBridge $screenBridge,
    ) {
    }

    public function respond(PageView $pageView): Response
    {
        return $this->renderer->render(
            self::TEMPLATE,
            [
                'screen' => $this->screenBridge->bridge($pageView, BridgeTarget::SCREEN_ACCESSING),
                'pageView' => $pageView,
            ],
            $pageView->statusCode,
        );
    }
}
