<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class ApiRateLimitListener implements EventSubscriberInterface
{
    private const API_PATH_PREFIX = '/api/';

    public function __construct(
        private bool $enabled,
        private array $excludedRoutes,
        private ApiRateLimiterFactoryInterface $rateLimiterFactory,
        private ClientIdentifierProviderInterface $clientIdentifierProvider
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$this->isApiRequest($request)) {
            return;
        }

        if (!$this->enabled) {
            return;
        }

        if ($this->isRouteExcluded($request->getPathInfo())) {
            return;
        }

        $limiter = $this->rateLimiterFactory->create($this->clientIdentifierProvider->getClientIdentifier($request));
        $limit = $limiter->consume();

        if (!$limit->isAccepted()) {
            $event->setResponse($this->createRateLimitExceededResponse($limit->getRetryAfter()));
            return;
        }

        $request->attributes->set('_rate_limit_info', [
            'limit' => $limit->getLimit(),
            'remaining' => $limit->getRemainingTokens(),
            'reset' => $limit->getRetryAfter()->getTimestamp(),
        ]);
    }

    private function isApiRequest(Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), self::API_PATH_PREFIX);
    }

    private function isRouteExcluded(string $route): bool
    {
        foreach ($this->excludedRoutes as $pattern) {
            if ($this->matchRoute($route, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function matchRoute(string $route, string $pattern): bool
    {
        if ($pattern === $route) {
            return true;
        }

        if (str_contains($pattern, '*')) {
            $regex = '/^' . str_replace(['/', '*'], ['\/', '.*'], $pattern) . '$/';
            return (bool) preg_match($regex, $route);
        }

        return false;
    }

    private function createRateLimitExceededResponse(\DateTimeImmutable $retryAfter): JsonResponse
    {
        $retryAfterSeconds = max(0, $retryAfter->getTimestamp() - time());

        $response = new JsonResponse(
            [
                'error' => 'rate_limit_exceeded',
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $retryAfterSeconds,
            ],
            Response::HTTP_TOO_MANY_REQUESTS
        );

        $response->headers->set('Retry-After', (string) $retryAfterSeconds);
        $response->headers->set('X-RateLimit-Reset', (string) $retryAfter->getTimestamp());

        return $response;
    }
}
