<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\Http;

use OxidEsales\Eshop\Core\Registry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class SessionCommitListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::RESPONSE => ['onKernelResponse', -900]];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest() || !Registry::getSession()->isSessionStarted()) {
            return;
        }

        Registry::getSession()->freeze();
    }
}
