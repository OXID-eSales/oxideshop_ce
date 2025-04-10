<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\RateLimiter;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\ApiRateLimiterFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use PHPUnit\Framework\TestCase;

class ApiRateLimiterFactoryTest extends TestCase
{
    public function testCreateReturnsLimiter(): void
    {
        $factory = $this->createFactory(limit: 100, interval: 60, policy: 'token_bucket');

        $this->assertTrue($factory->create('test_client_' . uniqid())->consume()->isAccepted());
    }

    public function testCreateSharesStateForSameIdentifier(): void
    {
        $factory = $this->createFactory(limit: 2, interval: 60, policy: 'token_bucket');
        $clientId = 'same_client_' . uniqid();

        $factory->create($clientId)->consume();
        $factory->create($clientId)->consume();

        $this->assertFalse($factory->create($clientId)->consume()->isAccepted());
    }

    public function testLimiterRejectsRequestsExceedingLimit(): void
    {
        $factory = $this->createFactory(limit: 3, interval: 60, policy: 'token_bucket');
        $clientId = 'test_client_' . uniqid();

        $this->assertTrue($factory->create($clientId)->consume()->isAccepted());
        $this->assertTrue($factory->create($clientId)->consume()->isAccepted());
        $this->assertTrue($factory->create($clientId)->consume()->isAccepted());
        $this->assertFalse($factory->create($clientId)->consume()->isAccepted());
    }

    public function testSlidingWindowPolicy(): void
    {
        $factory = $this->createFactory(limit: 3, interval: 60, policy: 'sliding_window');
        $clientId = 'test_client_' . uniqid();

        $this->assertTrue($factory->create($clientId)->consume()->isAccepted());
        $this->assertTrue($factory->create($clientId)->consume()->isAccepted());
        $this->assertTrue($factory->create($clientId)->consume()->isAccepted());
        $this->assertFalse($factory->create($clientId)->consume()->isAccepted());
    }

    private function createFactory(int $limit, int $interval, string $policy): ApiRateLimiterFactory
    {
        return new ApiRateLimiterFactory($limit, $interval, $policy, new BasicContext());
    }
}
