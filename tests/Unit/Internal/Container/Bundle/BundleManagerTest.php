<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Bundle;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Bundle\BundleManager;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\Bundle\TestBundle\TestBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * @internal
 */
class BundleManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        TestBundle::resetState();
    }

    public function testBootCallsBundleBoot(): void
    {
        $manager = new BundleManager();

        $manager->boot(new Container(), [new TestBundle()]);

        $this->assertTrue(TestBundle::$bootCalled);
    }

    public function testBootSetsBootedState(): void
    {
        $manager = new BundleManager();

        $this->assertFalse($manager->isBooted());
        $manager->boot(new Container(), [new TestBundle()]);
        $this->assertTrue($manager->isBooted());
    }

    public function testBootOnlyBootsOnce(): void
    {
        $manager = new BundleManager();

        $manager->boot(new Container(), [new TestBundle()]);
        TestBundle::resetState();
        $manager->boot(new Container(), [new TestBundle()]);

        $this->assertFalse(TestBundle::$bootCalled);
    }

    public function testShutdownCallsBundleShutdown(): void
    {
        $manager = new BundleManager();

        $manager->boot(new Container(), [new TestBundle()]);
        $manager->shutdown();

        $this->assertTrue(TestBundle::$shutdownCalled);
    }

    public function testShutdownResetsBootedState(): void
    {
        $manager = new BundleManager();

        $manager->boot(new Container(), [new TestBundle()]);
        $this->assertTrue($manager->isBooted());

        $manager->shutdown();
        $this->assertFalse($manager->isBooted());
    }

    public function testShutdownDoesNothingIfNotBooted(): void
    {
        $manager = new BundleManager();

        $manager->shutdown();

        $this->assertFalse(TestBundle::$shutdownCalled);
    }

    public function testBootWithNoBundles(): void
    {
        $manager = new BundleManager();

        $manager->boot(new Container(), []);

        $this->assertTrue($manager->isBooted());
    }

    public function testCanRebootAfterShutdown(): void
    {
        $bundle = new TestBundle();
        $manager = new BundleManager();

        $manager->boot(new Container(), [$bundle]);
        $manager->shutdown();
        TestBundle::resetState();
        $manager->boot(new Container(), [$bundle]);

        $this->assertTrue(TestBundle::$bootCalled);
        $this->assertTrue($manager->isBooted());
    }
}
