<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Container\Event;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
class ConfigurationChangedEventSubscriber implements EventSubscriberInterface
{

    /**
     * @param ProjectYamlChangedEvent $event
     */
    public function resetContainer(ProjectYamlChangedEvent $event)
    {
        ContainerFactory::resetContainer();
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [ProjectYamlChangedEvent::NAME => 'resetContainer'];
    }
}
