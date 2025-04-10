<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\CacheStorage;

class ApiRateLimiterFactory implements ApiRateLimiterFactoryInterface
{
    private ?RateLimiterFactory $factory = null;

    public function __construct(
        private readonly int $limit,
        private readonly int $interval,
        private readonly string $policy,
        private readonly BasicContextInterface $context
    ) {
    }

    public function create(string $clientIdentifier): LimiterInterface
    {
        return $this->getFactory()->create($clientIdentifier);
    }

    private function getFactory(): RateLimiterFactory
    {
        if ($this->factory === null) {
            $this->factory = $this->createFactory();
        }

        return $this->factory;
    }

    private function createFactory(): RateLimiterFactory
    {
        $config = [
            'id' => 'api',
            'policy' => $this->policy,
            'limit' => $this->limit,
        ];

        if ($this->policy === 'token_bucket') {
            $config['rate'] = [
                'interval' => sprintf('%d seconds', $this->interval),
                'amount' => $this->limit,
            ];
        } else {
            $config['interval'] = sprintf('%d seconds', $this->interval);
        }

        return new RateLimiterFactory(
            $config,
            $this->createStorage(),
            new LockFactory(new FlockStore($this->context->getCacheDirectory()))
        );
    }

    private function createStorage(): CacheStorage
    {
        $cache = new FilesystemAdapter(
            namespace: 'rate_limiter',
            defaultLifetime: $this->interval * 2,
            directory: $this->context->getCacheDirectory()
        );

        return new CacheStorage($cache);
    }
}
