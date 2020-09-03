<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIServiceWrapper;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ProjectDIConfig\TestModule\TestEventSubscriber;
use PHPUnit\Framework\TestCase;

final class DIServiceWrapperTest extends TestCase
{
    public function testGenerateServicesWithNoArgumentsButExistingShopAwareClass(): void
    {
        $service = new DIServiceWrapper(TestEventSubscriber::class, []);
        $this->assertTrue($service->isShopAware());
        $this->assertTrue($service->checkClassExists());
    }

    public function testGenerateServicWithExistingShopAwareClass(): void
    {
        $service = new DIServiceWrapper(TestEventSubscriber::class, ['class' => TestEventSubscriber::class]);
        $this->assertTrue($service->isShopAware());
        $this->assertTrue($service->checkClassExists());
    }

    public function testGenerateServicWithNotExistingClass(): void
    {
        $service = new DIServiceWrapper('JustSomeMadeUpClass', []);
        $this->assertFalse($service->isShopAware());
        $this->assertTrue($service->checkClassExists());
        $this->assertArrayNotHasKey('calls', $service->getServiceAsArray());
    }

    public function testGenerateServicesWithCallsArgumentsAndExistingShopAwareClass(): void
    {
        $service = new DIServiceWrapper(
            TestEventSubscriber::class,
            ['calls' => ['addHandler' => '@oxid_esales.module.setup.path_module_setting_handler']]
        );
        $this->assertTrue($service->checkClassExists());

        $serviceParams = $service->getServiceAsArray();
        $this->assertArrayHasKey('calls', $serviceParams);
        $this->assertArrayHasKey('addHandler', $serviceParams['calls']);
    }

    public function testAddActiveShops(): void
    {
        $newShopId = 42;
        $service = new DIServiceWrapper(TestEventSubscriber::class, []);

        $this->assertContains($newShopId, $service->addActiveShops([$newShopId]));
    }

    public function testRemoveActiveShops(): void
    {
        $newShopId = 42;
        $service = new DIServiceWrapper(TestEventSubscriber::class, []);
        $service->addActiveShops([$newShopId]);

        $this->assertNotContains($newShopId, $service->removeActiveShops([$newShopId]));
    }

    public function testHasActiveShops(): void
    {
        $newShopId = 42;
        $service = new DIServiceWrapper(TestEventSubscriber::class, []);
        $service->addActiveShops([$newShopId]);

        $this->assertTrue($service->hasActiveShops());
    }
    
}
