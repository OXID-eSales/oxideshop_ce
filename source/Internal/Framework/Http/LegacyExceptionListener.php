<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http;

use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\RedirectException;
use OxidEsales\EshopCommunity\Internal\Framework\Http\Exception\ResponseException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class LegacyExceptionListener implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private bool $debugMode = false,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['onKernelException', -10]];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof RedirectException) {
            $event->setResponse(new RedirectResponse($exception->getUrl(), $exception->getStatusCode()));
            return;
        }

        if ($exception instanceof ResponseException) {
            $event->setResponse($exception->getResponse());
            return;
        }

        $this->logger->error($exception->getMessage(), [$exception]);

        if ($this->debugMode) {
            return;
        }

        $file = OX_BASE_PATH . 'offline.html';
        $content = is_readable($file) ? file_get_contents($file) : 'Page not found.';
        $event->setResponse(new Response($content, 404));
    }
}
