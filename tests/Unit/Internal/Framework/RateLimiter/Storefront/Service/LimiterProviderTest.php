<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\RateLimiter\Storefront\Service;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\LimiterProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

final class LimiterProviderTest extends TestCase
{
    public function testProvidesLimiterEnforcingTheRule(): void
    {
        $provider = new LimiterProvider(new InMemoryStorage(), new LockFactory(new FlockStore()));
        $rule = ['id' => 'global', 'limit' => 1, 'interval' => '1 minute'];

        $this->assertTrue($provider->limiterFor($rule, 'k')->consume()->isAccepted());
        $this->assertFalse($provider->limiterFor($rule, 'k')->consume()->isAccepted());
    }
}
