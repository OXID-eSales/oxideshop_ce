<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\EventSubscriber;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Event\BeforeModuleDeactivationEvent;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Event\FinalizingModuleActivationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
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
     * @param ModuleConfigurationDaoInterface $ModuleConfigurationDao
     * @param ShopAdapterInterface            $shopAdapter
     */
    public function __construct(
        ModuleConfigurationDaoInterface $ModuleConfigurationDao,
        ShopAdapterInterface $shopAdapter
    ) {
        $this->moduleConfigurationDao = $ModuleConfigurationDao;
        $this->shopAdapter = $shopAdapter;
    }

    /**
     * @param FinalizingModuleActivationEvent $event
     */
    public function executeMetadataOnActivationEvent(FinalizingModuleActivationEvent $event)
    {
        $this->invalidateModuleCache($event);
        $this->executeMetadataEvent(
            'onActivate',
            $event->getModuleId(),
            $event->getShopId()
        );
    }

    /**
     * @param BeforeModuleDeactivationEvent $event
     */
    public function executeMetadataOnDeactivationEvent(BeforeModuleDeactivationEvent $event)
    {
        $this->executeMetadataEvent(
            'onDeactivate',
            $event->getModuleId(),
            $event->getShopId()
        );
    }

    /**
     * @param string $eventName
     * @param string $moduleId
     * @param int    $shopId
     */
    private function executeMetadataEvent(string $eventName, string $moduleId, int $shopId)
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        if ($moduleConfiguration->hasSetting(ModuleSetting::EVENTS)) {
            $events = $moduleConfiguration->getSetting(ModuleSetting::EVENTS)->getValue();

            if (\is_array($events) && array_key_exists($eventName, $events)) {
                \call_user_func($events[$eventName]);
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents() : array
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
