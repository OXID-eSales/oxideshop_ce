<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Event\RateLimitExceededEvent;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\RequestExclusionFilterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\RuleLimiterFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\RuleProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\KeyProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimit;

readonly class StorefrontRateLimiter implements StorefrontRateLimiterInterface
{
    public function __construct(
        private Request $request,
        private RuleProviderInterface $ruleProvider,
        private RequestExclusionFilterInterface $requestExclusionFilter,
        private KeyProviderInterface $keyProvider,
        private RuleLimiterFactoryInterface $limiterFactory,
        private LoggerInterface $logger,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function enforce(): void
    {
        if ($this->requestExclusionFilter->isExcluded($this->request)) {
            return;
        }

        foreach ($this->ruleProvider->getMatchingRules() as $rule) {
            $key = $this->keyProvider->get($rule, $this->request);
            if ($key !== '') {
                $this->consume($rule, $key);
            }
        }
    }

    private function consume(array $rule, string $key): void
    {
        try {
            $rateLimit = $this->limiterFactory->create($rule, $key)->consume();
        } catch (\Throwable $throwable) {
            $this->logger->error('Storefront rate limiter failed open.', ['exception' => $throwable]);
            return;
        }

        if (!$rateLimit->isAccepted()) {
            $this->reject((string) $rule['id'], $key, $this->retryAfter($rule), $rateLimit);
        }
    }

    private function retryAfter(array $rule): int
    {
        $now = time();

        return (new \DateTimeImmutable('@' . $now))->modify('+' . $rule['interval'])->getTimestamp() - $now;
    }

    private function reject(string $ruleId, string $key, int $retryAfter, RateLimit $rateLimit): never
    {
        $reset = time() + $retryAfter;

        $this->eventDispatcher->dispatch(new RateLimitExceededEvent($ruleId, $key, $retryAfter));

        throw new TooManyRequestsHttpException($retryAfter, 'Too many requests.', headers: [
            'X-RateLimit-Limit' => (string) $rateLimit->getLimit(),
            'X-RateLimit-Remaining' => (string) $rateLimit->getRemainingTokens(),
            'X-RateLimit-Reset' => (string) $reset,
        ]);
    }
}
