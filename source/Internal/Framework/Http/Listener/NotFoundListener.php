<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\Listener;

use OxidEsales\Eshop\Core\Exception\RoutingException;
use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class NotFoundListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['onKernelException', -30]];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$event->getThrowable() instanceof RoutingException || !$event->isMainRequest()) {
            return;
        }

        $event->allowCustomResponseCode();
        $event->setResponse($this->createResponse());
        $event->stopPropagation();
    }

    private function createResponse(): Response
    {
        try {
            return oxNew(ShopControl::class)->buildResponse('notfound', '')->setStatusCode(Response::HTTP_NOT_FOUND);
        } catch (RedirectException $redirect) {
            return new RedirectResponse($redirect->getUrl(), $redirect->getStatusCode());
        }
    }
}
