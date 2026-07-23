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

readonly class SecurityHeadersListener implements EventSubscriberInterface
{
    /**
     * @param string[] $headers
     */
    public function __construct(private array $headers)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::RESPONSE => ['onKernelResponse', 0]];
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $responseHeaders = $event->getResponse()->headers;
        foreach ($this->headers as $name => $value) {
            if ($value !== '' && !$responseHeaders->has($name)) {
                $responseHeaders->set($name, $value);
            }
        }
    }
}
