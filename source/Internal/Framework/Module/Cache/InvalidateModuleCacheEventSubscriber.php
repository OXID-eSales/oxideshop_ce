<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleActivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\ModuleSetupEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvalidateModuleCacheEventSubscriber implements EventSubscriberInterface
{
    /**
     * InvalidateModuleCacheEventSubscriber constructor.
     */
    public function __construct(private ModuleCacheServiceInterface $moduleCacheService)
    {
    }

    /**
     * @param ModuleSetupEvent $event
     */
    public function invalidateModuleCache(ModuleSetupEvent $event)
    {
        $this->moduleCacheService->invalidate($event->getModuleId(), $event->getShopId());
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FinalizingModuleActivationEvent::NAME   => 'invalidateModuleCache',
            FinalizingModuleDeactivationEvent::NAME => 'invalidateModuleCache',
        ];
    }
}
