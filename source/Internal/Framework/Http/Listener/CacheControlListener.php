<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class CacheControlListener implements EventSubscriberInterface
{
    private const CACHE_ATTRIBUTE_STATUS_CODES = [200, 203, 300, 301, 302, 304, 404, 410];

    public function __construct(private string $defaultCacheControl)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::RESPONSE => ['onKernelResponse', -960]];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();
        if ($this->isUserSpecific($request, $response)) {
            $this->makePrivate($response);
        }

        if ($this->defaultCacheControl !== '' && !$this->hasOwnCachePolicy($request, $response)) {
            $response->headers->set('Cache-Control', $this->defaultCacheControl);
        }
    }

    private function isUserSpecific(Request $request, Response $response): bool
    {
        return $request->attributes->getBoolean('_session_started')
            || $response->headers->getCookies() !== [];
    }

    private function hasOwnCachePolicy(Request $request, Response $response): bool
    {
        return $this->isCacheAttributeApplied($request, $response)
            || $response->getMaxAge() !== null
            || $response->isValidateable()
            || $response->headers->hasCacheControlDirective('public')
            || $response->headers->hasCacheControlDirective('no-store');
    }

    private function isCacheAttributeApplied(Request $request, Response $response): bool
    {
        return $request->attributes->has('_cache')
            && in_array($response->getStatusCode(), self::CACHE_ATTRIBUTE_STATUS_CODES, true);
    }

    private function makePrivate(Response $response): void
    {
        if (
            $response->headers->hasCacheControlDirective('public')
            || $response->headers->hasCacheControlDirective('s-maxage')
        ) {
            $response->setPrivate();
            $response->headers->removeCacheControlDirective('s-maxage');
        }
    }
}
