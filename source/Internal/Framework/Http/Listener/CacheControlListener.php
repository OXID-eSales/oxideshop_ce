<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class CacheControlListener implements EventSubscriberInterface
{
    private const SYMFONY_COMPUTED_DEFAULT = 'no-cache, private';

    public function __construct(private string $defaultCacheControl)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::RESPONSE => ['onKernelResponse', 0]];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest() || $this->defaultCacheControl === '') {
            return;
        }

        $response = $event->getResponse();
        if (
            $event->getRequest()->attributes->has('_cache')
            || $response->getEtag() !== null
            || $response->headers->get('Cache-Control') !== self::SYMFONY_COMPUTED_DEFAULT
        ) {
            return;
        }

        $response->headers->set('Cache-Control', $this->defaultCacheControl);
    }
}
