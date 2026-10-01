<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Event\RateLimitExceededEvent;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Exception\TooManyRequestsException;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\RequestExclusionFilterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\RuleLimiterFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\RuleProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\KeyProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimit;

class StorefrontRateLimiter implements StorefrontRateLimiterInterface
{
    private bool $enforced = false;

    public function __construct(
        private readonly Request $request,
        private readonly RuleProviderInterface $ruleProvider,
        private readonly RequestExclusionFilterInterface $requestExclusionFilter,
        private readonly KeyProviderInterface $keyProvider,
        private readonly RuleLimiterFactoryInterface $limiterFactory,
        private readonly LoggerInterface $logger,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function enforce(): void
    {
        if ($this->enforced) {
            return;
        }
        $this->enforced = true;

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

        throw new TooManyRequestsException(
            $retryAfter,
            $rateLimit->getLimit(),
            $rateLimit->getRemainingTokens(),
            $reset,
        );
    }
}
