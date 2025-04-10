<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class RateLimitHeadersListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse', -10],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $rateLimitInfo = $event->getRequest()->attributes->get('_rate_limit_info');

        if ($rateLimitInfo === null) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set('X-RateLimit-Limit', (string) $rateLimitInfo['limit']);
        $response->headers->set('X-RateLimit-Remaining', (string) $rateLimitInfo['remaining']);
        $response->headers->set('X-RateLimit-Reset', (string) $rateLimitInfo['reset']);
    }
}
