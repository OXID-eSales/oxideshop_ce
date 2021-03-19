<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Container\Event;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Event\ProjectYamlChangedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
