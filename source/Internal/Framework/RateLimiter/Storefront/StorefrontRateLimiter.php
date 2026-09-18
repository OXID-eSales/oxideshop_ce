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
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\StorageInterface;

readonly class StorefrontRateLimiter implements StorefrontRateLimiterInterface
{
    /**
     * @param string[] $excludedRoutes
     * @param string[] $excludedIps
     * @param array<int, array<string, mixed>> $rules
     */
    public function __construct(
        private array $excludedRoutes,
        private array $excludedIps,
        private array $rules,
        private Request $request,
        private StorageInterface $storage,
        private LockFactory $lockFactory,
        private ClientIdentifierProviderInterface $clientIdentifierProvider,
        private LoggerInterface $logger,
    ) {
    }

    public function enforce(): void
    {
        if ($this->isExcluded($this->request->getClientIp() ?? 'unknown', $this->request->getPathInfo())) {
            return;
        }

        $controllerKey = (string) $this->request->get('cl', '');
        $function = (string) $this->request->get('fnc', '');

        foreach ($this->rules as $rule) {
            if (!$this->matches($rule, $controllerKey, $function)) {
                continue;
            }

            $key = $this->key((string) $rule['key']);
            if ($key !== '') {
                $this->limit($rule, $key);
            }
        }
    }

    /**
     * @param array<string, mixed> $rule
     */
    private function matches(array $rule, string $controllerKey, string $function): bool
    {
        if (isset($rule['fnc']) && !in_array(strtolower($function), array_map('strtolower', (array) $rule['fnc']), true)) {
            return false;
        }

        return !isset($rule['cl']) || strtolower((string) $rule['cl']) === strtolower($controllerKey);
    }

    private function key(string $type): string
    {
        return match ($type) {
            'user' => $this->clientIdentifierProvider->getClientIdentifier($this->request),
            'email' => strtolower(trim((string) $this->request->request->get('lgn_usr', ''))),
            default => $this->request->getClientIp() ?? 'unknown',
        };
    }

    /**
     * @param array<string, mixed> $rule
     */
    private function limit(array $rule, string $key): void
    {
        try {
            $rateLimit = $this->factory($rule)->create($key)->consume();
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

    /**
     * @param array<string, mixed> $rule
     */
    private function factory(array $rule): RateLimiterFactory
    {
        return new RateLimiterFactory(
            [
                'id' => (string) $rule['id'],
                'policy' => 'sliding_window',
                'limit' => (int) $rule['limit'],
                'interval' => (string) $rule['interval'],
            ],
            $this->storage,
            $this->lockFactory,
        );
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
