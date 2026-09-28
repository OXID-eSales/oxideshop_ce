<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\RateLimiter\Storefront\Service;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service\RuleLimiterFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

final class RuleLimiterFactoryTest extends TestCase
{
    public function testCreatesLimiterEnforcingTheRule(): void
    {
        $factory = new RuleLimiterFactory(new InMemoryStorage(), new LockFactory(new FlockStore()));
        $rule = ['id' => 'global', 'limit' => 1, 'interval' => '1 minute'];

        $this->assertTrue($factory->create($rule, 'k')->consume()->isAccepted());
        $this->assertFalse($factory->create($rule, 'k')->consume()->isAccepted());
    }
}
