<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\EventSubscriber;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\BeforeModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleActivationEvent;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DispatchLegacyEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var ModuleConfigurationDaoInterface
     */
    private $moduleConfigurationDao;
    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    public function __construct(
        ModuleConfigurationDaoInterface $ModuleConfigurationDao,
        ShopAdapterInterface $shopAdapter
    ) {
        $this->moduleConfigurationDao = $ModuleConfigurationDao;
        $this->shopAdapter = $shopAdapter;
    }

    public function executeMetadataOnActivationEvent(FinalizingModuleActivationEvent $event): void
    {
        $this->executeMetadataEvent(
            'onActivate',
            $event->getModuleId(),
            $event->getShopId()
        );
    }

    public function executeMetadataOnDeactivationEvent(BeforeModuleDeactivationEvent $event): void
    {
        $this->executeMetadataEvent(
            'onDeactivate',
            $event->getModuleId(),
            $event->getShopId()
        );
    }

    private function executeMetadataEvent(string $eventName, string $moduleId, int $shopId): void
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        if ($moduleConfiguration->hasEvents()) {
            $events = [];

            foreach ($moduleConfiguration->getEvents() as $event) {
                $events[$event->getAction()] = $event->getMethod();
            }

            if (\is_array($events) && \array_key_exists($eventName, $events)) {
                \call_user_func($events[$eventName]);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FinalizingModuleActivationEvent::NAME => 'executeMetadataOnActivationEvent',
            BeforeModuleDeactivationEvent::NAME => 'executeMetadataOnDeactivationEvent',
        ];
    }
}
