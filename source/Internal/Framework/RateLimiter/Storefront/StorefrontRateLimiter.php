<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\ClientIdentifierProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Exception\TooManyRequestsException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;

readonly class StorefrontRateLimiter implements StorefrontRateLimiterInterface
{
    /**
     * @param string[] $excludedRoutes
     * @param string[] $excludedIps
     */
    public function __construct(
        private array $excludedRoutes,
        private array $excludedIps,
        private Request $request,
        private RateLimiterFactory $rateLimiterFactory,
        private ClientIdentifierProviderInterface $clientIdentifierProvider,
        private LoggerInterface $logger,
    ) {
    }

    public function enforce(): void
    {
        if ($this->isExcluded($this->request->getClientIp() ?? 'unknown', $this->request->getPathInfo())) {
            return;
        }

        try {
            $clientIdentifier = $this->clientIdentifierProvider->getClientIdentifier($this->request);
            $rateLimit = $this->rateLimiterFactory->create($clientIdentifier)->consume();
        } catch (\Throwable $throwable) {
            $this->logger->error('Storefront rate limiter failed open: ' . $throwable->getMessage(), [$throwable]);
            return;
        }

        if (!$rateLimit->isAccepted()) {
            throw new TooManyRequestsException(
                max(0, $rateLimit->getRetryAfter()->getTimestamp() - time()),
                $rateLimit->getLimit(),
                $rateLimit->getRemainingTokens(),
                $rateLimit->getRetryAfter()->getTimestamp(),
            );
        }
    }

    private function isExcluded(string $clientIdentifier, string $path): bool
    {
        return in_array($clientIdentifier, $this->excludedIps, true) || $this->isExcludedRoute($path);
    }

    private function isExcludedRoute(string $path): bool
    {
        foreach ($this->excludedRoutes as $pattern) {
            if ($pattern === $path) {
                return true;
            }

            if (
                str_contains($pattern, '*')
                && preg_match('/^' . str_replace(['/', '*'], ['\/', '.*'], $pattern) . '$/', $path)
            ) {
                return true;
            }
        }

        return false;
    }
}
