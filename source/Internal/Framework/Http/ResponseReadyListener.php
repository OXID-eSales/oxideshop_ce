<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class ResponseReadyListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [ResponseReadyEvent::class => ['stopRequest', -1000]];
    }

    public function stopRequest(ResponseReadyEvent $event): void
    {
        throw new ResponseReady($event->getResponse());
    }
}
