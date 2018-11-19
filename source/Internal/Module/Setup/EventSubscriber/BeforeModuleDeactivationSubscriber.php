<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\EventSubscriber;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\Provider\ModuleConfigurationProviderInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Event\BeforeModuleDeactivationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
class BeforeModuleDeactivationSubscriber implements EventSubscriberInterface
{

    /**
     * @var ModuleConfigurationProviderInterface
     */
    private $moduleConfigurationProvider;

    /**
     * @param ModuleConfigurationProviderInterface $moduleConfigurationProvider
     */
    public function __construct(ModuleConfigurationProviderInterface $moduleConfigurationProvider)
    {
        $this->moduleConfigurationProvider = $moduleConfigurationProvider;
    }

    /**
     * @param BeforeModuleDeactivationEvent $event
     */
    public function executeMetadataOnDeactivationEvent(BeforeModuleDeactivationEvent $event)
    {
        $moduleConfiguration = $this->moduleConfigurationProvider->getModuleConfiguration(
            $event->getModuleId(),
            $event->getEnvironmentName(),
            $event->getShopId()
        );
        $events = $moduleConfiguration->getSetting('events')->getValue();
        if (is_array($events) && array_key_exists('onDeactivate', $events)) {
            call_user_func($events['onDeactivate']);
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents() : array
    {
        return [
            BeforeModuleDeactivationEvent::NAME => 'executeMetadataOnDeactivationEvent',
        ];
    }
}
