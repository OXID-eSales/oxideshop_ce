<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Event\RateLimitExceededEvent;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Exception\TooManyRequestsException;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\LimiterProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\RequestExclusionsInterface;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\RuleProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\ThrottleKeyProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Request\RequestFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\RateLimiter\RateLimit;

class StorefrontRateLimiter implements StorefrontRateLimiterInterface
{
    private bool $enforced = false;

    public function __construct(
        private readonly RequestFactory $requestFactory,
        private readonly RuleProviderInterface $ruleProvider,
        private readonly RequestExclusionsInterface $requestExclusions,
        private readonly ThrottleKeyProviderInterface $throttleKeyProvider,
        private readonly LimiterProviderInterface $limiterProvider,
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

        $request = $this->requestFactory->create();

        if ($this->requestExclusions->excludes($request)) {
            return;
        }

        foreach ($this->ruleProvider->rulesFor($request) as $rule) {
            $key = $this->throttleKeyProvider->keyFor($rule, $request);
            if ($key !== '') {
                $this->consume($rule, $key);
            }
        }
    }

    private function consume(array $rule, string $key): void
    {
        try {
            $rateLimit = $this->limiterProvider->limiterFor($rule, $key)->consume();
        } catch (\Throwable $throwable) {
            $this->logger->error('Storefront rate limiter failed open.', ['exception' => $throwable]);
            return;
        }

        if (!$rateLimit->isAccepted()) {
            $this->reject((string) $rule['id'], $key, $rateLimit);
        }
    }

    private function reject(string $ruleId, string $key, RateLimit $rateLimit): never
    {
        $reset = $rateLimit->getRetryAfter()->getTimestamp();
        $retryAfter = max(0, $reset - time());

        $this->eventDispatcher->dispatch(new RateLimitExceededEvent($ruleId, $key, $retryAfter));

        throw new TooManyRequestsException(
            $retryAfter,
            $rateLimit->getLimit(),
            $rateLimit->getRemainingTokens(),
            $reset,
        );
    }
}
