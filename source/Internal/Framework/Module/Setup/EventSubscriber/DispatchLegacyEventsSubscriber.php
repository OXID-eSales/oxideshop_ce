<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\EventSubscriber;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\EventModuleParamerterServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\BeforeModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event\FinalizingModuleActivationEvent;
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
    /**
     * @var EventModuleParamerterServiceInterface
     */
    private $eventModuleParamerterService;

    /**
     * @param ModuleConfigurationDaoInterface $ModuleConfigurationDao
     * @param ShopAdapterInterface            $shopAdapter
     */
    public function __construct(
        ModuleConfigurationDaoInterface $ModuleConfigurationDao,
        ShopAdapterInterface $shopAdapter,
        EventModuleParamerterServiceInterface $eventModuleParamerter
    ) {
        $this->moduleConfigurationDao = $ModuleConfigurationDao;
        $this->shopAdapter = $shopAdapter;
        $this->eventModuleParamerterService = $eventModuleParamerter;
    }

    /**
     * @param FinalizingModuleActivationEvent $event
     */
    public function executeMetadataOnActivationEvent(FinalizingModuleActivationEvent $event)
    {
        $this->invalidateModuleCache($event);

        $this->eventModuleParamerterService->forActivate(
            function ($parameters) use ($event) {

                $this->executeMetadataEvent(
                    'onActivate',
                    $event->getModuleId(),
                    $event->getShopId(),
                    $parameters
                );

            }
        );

    }

    /**
     * @param BeforeModuleDeactivationEvent $event
     */
    public function executeMetadataOnDeactivationEvent(BeforeModuleDeactivationEvent $event)
    {
        $this->eventModuleParamerterService->forDeactivate(
            function ($parameters) use ($event) {

                $this->executeMetadataEvent(
                    'onDeactivate',
                    $event->getModuleId(),
                    $event->getShopId(),
                    $parameters
                );

            }
        );
    }

    /**
     * @param string $eventName
     * @param string $moduleId
     * @param int    $shopId
     * @param array  $parameters
     */
    private function executeMetadataEvent(string $eventName, string $moduleId, int $shopId, array $parameters)
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        if ($moduleConfiguration->hasEvents()) {
            $events = [];

            foreach ($moduleConfiguration->getEvents() as $event) {
                $events[$event->getAction()] = $event->getMethod();
            }

            if (\is_array($events) && array_key_exists($eventName, $events)) {
                \call_user_func($events[$eventName], ...$parameters);
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FinalizingModuleActivationEvent::NAME   => 'executeMetadataOnActivationEvent',
            BeforeModuleDeactivationEvent::NAME     => 'executeMetadataOnDeactivationEvent',
        ];
    }

    /**
     * This is needed only for the modules which has non namespaced classes.
     * This method MUST be removed when support for non namespaced modules will be dropped (metadata v1.*).
     *
     * @param FinalizingModuleActivationEvent $event
     */
    private function invalidateModuleCache(FinalizingModuleActivationEvent $event)
    {
        $this->shopAdapter->invalidateModuleCache($event->getModuleId());
    }
}
