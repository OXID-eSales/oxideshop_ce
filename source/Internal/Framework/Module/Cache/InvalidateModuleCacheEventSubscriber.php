<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Event\ModuleConfigurationChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleActivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\ModuleSetupEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class InvalidateModuleCacheEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private ModuleCacheServiceInterface $moduleCacheService)
    {
    }

    public function invalidateModuleCache(
        ModuleSetupEvent|ModuleConfigurationChangedEvent $event
    ): void {
        $this->moduleCacheService->invalidate($event->getModuleId(), $event->getShopId());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FinalizingModuleActivationEvent::class   => 'invalidateModuleCache',
            FinalizingModuleDeactivationEvent::class => 'invalidateModuleCache',
            ModuleConfigurationChangedEvent::class => 'invalidateModuleCache',
        ];
    }
}
