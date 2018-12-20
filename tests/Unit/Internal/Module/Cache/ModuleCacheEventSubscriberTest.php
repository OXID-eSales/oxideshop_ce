<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Cache;

use OxidEsales\EshopCommunity\Internal\Module\Cache\ModuleCacheEventSubscriber;
use OxidEsales\EshopCommunity\Internal\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Event\FinalizingModuleActivationEvent;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Event\FinalizingModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Event\ModuleSetupEvent;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleCacheEventSubscriberTest extends TestCase
{
    public function testSubscribedEvents()
    {
        $this->assertSame(
            [
                FinalizingModuleActivationEvent::NAME   => 'invalidateModuleCache',
                FinalizingModuleDeactivationEvent::NAME => 'invalidateModuleCache',
            ],
            ModuleCacheEventSubscriber::getSubscribedEvents()
        );
    }

    public function testSubscriberCallsModuleCacheService()
    {
        $moduleCacheService = $this->getMockBuilder(ModuleCacheServiceInterface::class)->getMock();
        $moduleCacheService
            ->expects($this->once())
            ->method('invalidateModuleCache');

        $event = new class(1, 'testModuleId') extends ModuleSetupEvent {};

        $subscriber = new ModuleCacheEventSubscriber($moduleCacheService);
        $subscriber->invalidateModuleCache($event);
    }
}
