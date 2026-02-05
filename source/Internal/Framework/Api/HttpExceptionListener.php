<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Api;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class HttpExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', -10],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $response = new JsonResponse(
                ['error' => $this->getErrorMessage($exception)],
                $exception->getStatusCode(),
                $exception->getHeaders()
            );
            $event->setResponse($response);
        }
    }

    private function getErrorMessage(HttpExceptionInterface $exception): string
    {
        if (getenv('OXID_DEBUG_MODE')) {
            return $exception->getMessage();
        }

        return Response::$statusTexts[$exception->getStatusCode()] ?? 'An error occurred';
    }
}
