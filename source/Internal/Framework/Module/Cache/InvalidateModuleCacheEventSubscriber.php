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
     * @var ModuleCacheServiceInterface
     */
    private $moduleCacheService;

    /**
     * InvalidateModuleCacheEventSubscriber constructor.
     */
    public function __construct(ModuleCacheServiceInterface $moduleCacheService)
    {
        $this->moduleCacheService = $moduleCacheService;
    }

    public function invalidateModuleCache(ModuleSetupEvent $event): void
    {
        $this->moduleCacheService->invalidate($event->getModuleId(), $event->getShopId());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FinalizingModuleActivationEvent::NAME => 'invalidateModuleCache',
            FinalizingModuleDeactivationEvent::NAME => 'invalidateModuleCache',
        ];
    }
}
