<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class HttpExceptionListener implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => ['onKernelException', -20]];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $expectsJson = $event->getRequest()->getPreferredFormat() === 'json';

        if (!$exception instanceof HttpExceptionInterface && !$expectsJson) {
            return;
        }

        if (!$exception instanceof HttpExceptionInterface) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }

        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;
        $headers = $exception instanceof HttpExceptionInterface ? $exception->getHeaders() : [];
        $statusText = Response::$statusTexts[$statusCode] ?? '';

        $event->setResponse(
            $expectsJson
                ? new JsonResponse(['error' => $statusText], $statusCode, $headers)
                : new Response($statusText, $statusCode, $headers)
        );
        $event->stopPropagation();
    }
}
