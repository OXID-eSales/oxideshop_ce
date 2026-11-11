<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\Listener;

use OxidEsales\Eshop\Core\SeoDecoder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class SeoUrlListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 32]];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$event->isMainRequest() || !str_ends_with($request->getScriptName(), 'oxseo.php')) {
            return;
        }

        oxNew(SeoDecoder::class)->processSeoCall(
            $request->getRequestUri(),
            str_replace('oxseo.php', '', $request->getScriptName())
        );
        $request->query->add($_GET);
    }
}
