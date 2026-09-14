<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\StorageInterface;

readonly class LimiterProvider implements LimiterProviderInterface
{
    public function __construct(
        private StorageInterface $storage,
        private LockFactory $lockFactory,
    ) {
    }

    /**
     * @param array<string, mixed> $rule
     */
    public function limiterFor(array $rule, string $key): LimiterInterface
    {
        $factory = new RateLimiterFactory(
            [
                'id' => (string) $rule['id'],
                'policy' => 'sliding_window',
                'limit' => (int) $rule['limit'],
                'interval' => (string) $rule['interval'],
            ],
            $this->storage,
            $this->lockFactory,
        );

        return $factory->create($key);
    }
}
