<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\Listener;

use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class RedirectListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['onKernelException', 0]];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();
        if (!$throwable instanceof RedirectException) {
            return;
        }

        $event->allowCustomResponseCode();
        $event->setResponse($this->createResponse($throwable));
        $event->stopPropagation();
    }

    private function createResponse(RedirectException $exception): Response
    {
        return new RedirectResponse($exception->getUrl(), $this->redirectStatusCode($exception));
    }

    private function redirectStatusCode(RedirectException $exception): int
    {
        $statusCode = $exception->getStatusCode();

        return $statusCode >= 300 && $statusCode < 400 ? $statusCode : Response::HTTP_FOUND;
    }
}
