<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

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
     * @param ModuleCacheServiceInterface $moduleCacheService
     */
    public function __construct(ModuleCacheServiceInterface $moduleCacheService)
    {
        $this->moduleCacheService = $moduleCacheService;
    }

    /**
     * @param ModuleSetupEvent $event
     */
    public function invalidateModuleCache(ModuleSetupEvent $event)
    {
        $this->moduleCacheService->invalidateModuleCache($event->getModuleId(), $event->getShopId());
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
