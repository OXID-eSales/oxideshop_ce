<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\Listener;

use OxidEsales\Eshop\Core\Exception\RoutingException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class JsonNotFoundListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['onKernelException', 0]];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (
            !$exception instanceof RoutingException
            || !$event->isMainRequest()
            || $event->getRequest()->getPreferredFormat() !== 'json'
        ) {
            return;
        }

        $event->setThrowable(new NotFoundHttpException($exception->getMessage(), $exception));
    }
}
